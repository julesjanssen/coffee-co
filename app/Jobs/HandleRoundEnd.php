<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\ScoreType;
use App\Enums\GameSession\Status;
use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status as ProjectStatus;
use App\Enums\Queue;
use App\Events\GameSessionRoundStatusUpdated;
use App\Models\GameSession;
use App\Models\Project;
use App\Models\ProjectHistoryItem;
use App\Values\GameRound;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleRoundEnd implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected GameSession $session,
        protected GameRound $round,
    ) {
        $this->onQueue(Queue::GAME);
    }

    public function handle()
    {
        if ($this->session->current_round_id !== $this->round->roundID) {
            return;
        }

        if ($this->session->round_status->isNot(RoundStatus::ACTIVE)) {
            return;
        }

        if (! $this->session->reserve($this->getReservationKey(), '+30 seconds')) {
            $this->release(10);

            return;
        }

        $this->preProcessing();
        $this->process();
        $this->postProcessing();

        $this->session->release($this->getReservationKey());
    }

    private function preProcessing()
    {
        $this->session->update([
            'round_status' => RoundStatus::PROCESSING,
        ]);

        GameSessionRoundStatusUpdated::dispatch($this->session);
    }

    private function process()
    {
        $this->processProjects();

        if ($this->round->isLastRoundOfQuarter()) {
            $this->processNpsDeltasForUptime();
        }

        if ($this->round->isLastRoundOfYear()) {
            $this->trackMarketShare();
        }

        $this->session->update([
            'round_status' => RoundStatus::PROCESSED,
        ]);
    }

    private function postProcessing()
    {
        $this->session->update([
            'round_status' => RoundStatus::PROCESSED,
        ]);

        if ($this->round->roundID >= $this->session->scenario->numberOfRounds()) {
            $this->session->update([
                'status' => Status::FINISHED,
            ]);
        }

        if ($this->session->settings->shouldPauseAfterCurrentRound) {
            $this->session->pause();
        } else {
            // start new round
            HandleRoundStart::dispatch($this->session, $this->round->next());
        }

        GameSessionRoundStatusUpdated::dispatch($this->session);
    }

    private function processProjects()
    {
        $this->updateProjectFailureChances();
        $this->updateProjectStates();
    }

    private function updateProjectFailureChances()
    {
        $this->session->projects()
            ->with([
                'request',
            ])
            ->where('status', '=', ProjectStatus::ACTIVE)
            ->increment('failure_chance', $this->session->settings->failChanceIncreasePerRound);
    }

    private function updateProjectStates()
    {
        $this->session->projects()
            ->with(['request'])
            ->whereNotIn('status', [
                ProjectStatus::FINISHED,
                ProjectStatus::LOST,
            ])
            ->get()
            ->each($this->updateProjectState(...))
            ->each($this->trackProjectState(...))
            // ->each($this->calcUptime(...))
            ->each($this->handleProjectLifetime(...));
    }

    private function updateProjectState(Project $project)
    {
        switch ($project->status) {
            case ProjectStatus::ACTIVE:
                if (random_int(0, 100) < $project->failure_chance) {
                    $project->update([
                        'status' => ProjectStatus::DOWN,
                        'down_round_id' => $this->round->roundID,
                    ]);
                }
                break;

            case ProjectStatus::DOWN:
                $project->increment('downtime');
                break;

            case ProjectStatus::WON:
                $maxDeliveryRound = $project->quote_round_id + $this->session->settings->roundsToDeliverProject;
                if ($maxDeliveryRound <= $this->session->currentRound->roundID) {
                    // not delivered in time
                    $project->update([
                        'status' => ProjectStatus::LOST,
                    ]);
                }
                break;

            case ProjectStatus::PENDING:
                $maxQuoteRound = $project->request_round_id + $this->session->settings->roundsToSubmitOffer;
                if ($maxQuoteRound <= $this->session->currentRound->roundID) {
                    // not quoted in time
                    $project->update([
                        'status' => ProjectStatus::LOST,
                    ]);
                }
                break;
        }
    }

    private function trackProjectState(Project $project)
    {
        if ($project->status->notIn([
            ProjectStatus::ACTIVE,
            ProjectStatus::DOWN,
        ])) {
            return;
        }

        $project->historyItems()->create([
            'round_id' => $project->session->currentRound->roundID,
            'status' => $project->status,
        ]);
    }

    private function handleProjectLifetime(Project $project)
    {
        if ($project->status->notIn([
            ProjectStatus::ACTIVE,
            ProjectStatus::DOWN,
        ])) {
            return;
        }

        $finishRound = $project->delivery_round_id + $project->request->duration;
        if ($finishRound > $this->session->currentRound->roundID) {
            return;
        }

        $project->update([
            'status' => ProjectStatus::FINISHED,
        ]);

        $uptime = $project->uptimePercentage();
        $bonus = 0;
        if ($uptime > 80) {
            $bonus = (int) ceil($project->price * .1);
        } elseif ($uptime > 70) {
            $bonus = (int) ceil($project->price * .05);
        }

        $project->session->transactions()
            ->create([
                'project_id' => $project->id,
                'client_id' => $project->client_id,
                'type' => TransactionType::PROJECT_UPTIME_BONUS,
                'details' => [
                    'uptime' => (int) round($uptime),
                ],
                'round_id' => $this->round->roundID,
                'value' => $bonus,
            ]);
    }

    private function processNpsDeltasForUptime()
    {
        $quarterRange = range($this->round->roundID - 2, $this->round->roundID);
        $avgUptime = vsprintf("AVG(CASE WHEN ph.status = '%s' THEN 100 WHEN ph.status = '%s' THEN 0 END) as avg_uptime", [
            ProjectStatus::ACTIVE->value,
            ProjectStatus::DOWN->value,
        ]);

        ProjectHistoryItem::query()
            ->from('project_history as ph')
            ->join('projects as p', function ($query) {
                $query->on('p.id', '=', 'ph.project_id');
            })
            /** @phpstan-ignore argument.type */
            ->where('p.game_session_id', '=', $this->session->id)
            ->whereIn('ph.round_id', $quarterRange)
            ->whereIn('ph.status', [
                ProjectStatus::ACTIVE,
                ProjectStatus::DOWN,
            ])
            ->select('p.client_id')
            ->selectRaw($avgUptime)
            ->groupBy('client_id')
            ->toBase()
            ->get()
            ->each(function ($result) {
                $uptime = (float) $result->avg_uptime;

                $score = 0;
                if ($uptime > 90) {
                    $score = 1;
                } elseif ($uptime < 60) {
                    $score = -3;
                } elseif ($uptime < 70) {
                    $score = -2;
                } elseif ($uptime < 80) {
                    $score = -1;
                }

                $this->session->scores()
                    ->create([
                        'client_id' => $result->client_id,
                        'type' => ScoreType::NPS,
                        'trigger_type' => 'quarter-uptime',
                        'event' => 'Q' . $this->round->quarter(),
                        'round_id' => $this->round->roundID,
                        'details' => [
                            'uptime' => (int) round($uptime),
                        ],
                        'value' => $score,
                    ]);
            });
    }

    private function trackMarketShare()
    {
        $yearRange = GameRound::getRangeForYear($this->round->year());

        $projectValue = $this->session->projects()
            ->with(['request'])
            ->whereIn('quote_round_id', $yearRange)
            ->get()
            ->sum(fn($v) => $v->request->settings->value);

        $marketValue = $this->session->scenario->requests()
            ->whereIn('delay', $yearRange)
            ->get()
            ->sum(fn($v) => $v->settings->value);

        if ((int) $marketValue === 0) {
            return;
        }

        $marketShare = (int) round(($projectValue / $marketValue) * 100);

        $this->session->scores()
            ->create([
                'type' => ScoreType::MARKETSHARE,
                'trigger_type' => 'year-end',
                'event' => $this->round->year(),
                'round_id' => $this->round->roundID,
                'value' => $marketShare,
            ]);
    }

    private function getReservationKey()
    {
        return self::class . ':' . $this->round->roundID;
    }
}
