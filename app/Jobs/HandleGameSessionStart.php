<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\Status;
use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status as ProjectStatus;
use App\Enums\Queue;
use App\Models\GameSession;
use App\Models\Project;
use App\Models\Scenario;
use App\Models\ScenarioRequest;
use App\Values\GameRound;
use App\Values\ProjectSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleGameSessionStart implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected GameSession $session,
    ) {
        $this->onQueue(Queue::GAME);
    }

    public function handle()
    {
        if ($this->session->status->isNot(Status::PENDING)) {
            return;
        }

        if (! $this->session->reserve($this->getReservationKey(), '+30 seconds')) {
            $this->release(10);

            return;
        }

        $this->process();

        $this->session->release($this->getReservationKey());
    }

    private function process()
    {
        $scenario = $this->session->pickRelevantScenario();

        $this->processInitialProjects($scenario);

        $this->session->update([
            'status' => Status::PLAYING,
            'scenario_id' => $scenario->id,
        ]);

        $this->session->refresh();

        $round = new GameRound($this->session->scenario, 1);

        HandleRoundStart::dispatchSync($this->session, $round);
    }

    private function processInitialProjects(Scenario $scenario)
    {
        $scenario->requests()
            ->with(['client'])
            ->where('delay', '=', 0)
            ->get()
            ->each($this->handleInitialProject(...));
    }

    private function handleInitialProject(ScenarioRequest $request)
    {
        $status = $request->settings->initialstatus;
        $project = Project::fromRequest($request);

        $project->fill([
            'game_session_id' => $this->session->id,
            'failure_chance' => $request->settings->initialfailurechance,
            'status' => $status,
            'request_round_id' => 1,
            'settings' => ProjectSettings::fromArray([
                'labConsultingApplied' => false,
                'labConsultingIncluded' => false,
            ]),
        ]);

        if ($status->in([
            ProjectStatus::ACTIVE,
            ProjectStatus::WON,
            ProjectStatus::DOWN,
            ProjectStatus::FINISHED,
        ])) {
            $solution = $request->solutions()->first();

            $project->fill([
                'solution_id' => $solution->id,
            ]);
        }

        if ($status->is(ProjectStatus::WON)) {
            $project->fill([
                'quote_round_id' => 1,
            ]);
        }

        if ($status->is(ProjectStatus::ACTIVE)) {
            $project->fill([
                'quote_round_id' => 1,
                'delivery_round_id' => 1,
            ]);
        }

        $project->save();

        if ($status->in([
            ProjectStatus::ACTIVE,
            ProjectStatus::WON,
            ProjectStatus::DOWN,
            ProjectStatus::FINISHED,
        ])) {
            $this->session->transactions()
                ->create([
                    'project_id' => $project->id,
                    'client_id' => $project->client_id,
                    'type' => TransactionType::PROJECT_WON,
                    'round_id' => 1,
                    'value' => (int) round($project->price * .9),
                ]);
        }
    }

    private function getReservationKey()
    {
        return self::class;
    }
}
