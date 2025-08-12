<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Campaign;

use App\Enums\GameSession\ScoreType;
use App\Enums\GameSession\TransactionType;
use App\Enums\Scenario\CampaignCodeCategory;
use App\Models\GameCampaignCode;
use App\Models\GameSession;
use App\Models\ScenarioCampaignCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        return Inertia::render('game/marketing/campaign/view');
    }

    public function store(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $request->validate([
            'code' => ['array', 'required', 'size:4'],
            'code.*' => ['required', 'integer', 'min:0', 'max:9'],
        ]);

        $code = $this->findCampaignCode($request, $session);

        $used = GameCampaignCode::query()
            ->where('game_session_id', '=', $session->id)
            ->where('code_id', '=', $code->id)
            ->exists();

        if ($used) {
            throw ValidationException::withMessages([
                'code' => [__('Code already used.')],
            ]);
        }

        $hints = $this->getHintsForCode($session, $code);

        $gameCode = GameCampaignCode::create([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'code_id' => $code->id,
            'round_id' => $session->currentRound->roundID,
            'details' => [
                'hints' => $hints,
            ],
        ]);

        $session->transactions()
            ->create([
                'participant_id' => $participant->id,
                'type' => TransactionType::MARKETING_CAMPAIGN,
                'round_id' => $session->currentRound->roundID,
                'value' => $code->category->cost() * -1,
            ]);

        $codeCategory = $code->category;
        if ($codeCategory->kpiScore() > 0) {
            $session->scores()
                ->create([
                    'participant_id' => $participant->id,
                    'type' => ScoreType::MARKETING_KPI,
                    'trigger_type' => $gameCode->getMorphClass(),
                    'trigger_id' => $gameCode->getKey(),
                    'event' => 'campaign',
                    'round_id' => $session->currentRound->roundID,
                    'value' => $codeCategory->kpiScore(),
                ]);
        }

        if ($codeCategory->tresholdScore() > 0) {
            $session->scores()
                ->create([
                    'participant_id' => $participant->id,
                    'type' => ScoreType::MARKETING_TRESHOLD,
                    'trigger_type' => $gameCode->getMorphClass(),
                    'trigger_id' => $gameCode->getKey(),
                    'event' => 'campaign',
                    'round_id' => $session->currentRound->roundID,
                    'value' => $codeCategory->tresholdScore(),
                ]);
        }

        return [
            'hints' => $gameCode->details->hints,
        ];
    }

    private function getHintsForCode(GameSession $session, ScenarioCampaignCode $code): array
    {
        if ($code->category->is(CampaignCodeCategory::EASY)) {
            return $this->getHintsForEasyCode($session, $code);
        }

        return $this->getHintsForDifficultCode($session, $code);
    }

    private function getHintsForEasyCode(GameSession $session, ScenarioCampaignCode $code)
    {
        $codesUsed = GameCampaignCode::query()
            ->where('game_session_id', '=', $session->id)
            ->whereRelation('code', fn($q) => $q->where('category', '=', CampaignCodeCategory::EASY))
            ->count();

        $hints = $session->scenario->tips->pluck('content')->toArray();
        $hints = $session->randomizer()->shuffleArray($hints);

        $index = $codesUsed % count($hints);

        return [$hints[$index]];
    }

    private function getHintsForDifficultCode(GameSession $session, ScenarioCampaignCode $code)
    {
        $chunks = 6;
        $defaultHint = __('There is no new information, but you have increased your access in the market.');

        $codesUsed = GameCampaignCode::query()
            ->where('game_session_id', '=', $session->id)
            ->whereRelation('code', fn($q) => $q->where('category', '=', CampaignCodeCategory::DIFFICULT))
            ->count();

        if ($codesUsed >= $chunks) {
            return [$defaultHint];
        }

        $hints = $session->scenario->clients
            ->map(fn($c) => $c->segment->getHintMessageForClient($c))
            ->toArray();

        return $session->randomizer()->splitItemsRandomly($hints, $chunks)[$codesUsed] ?? [$defaultHint];
    }

    private function findCampaignCode(Request $request, GameSession $session)
    {
        $code = $request->collect('code')->join('');

        if (! App::isProduction() && $code === '2222') {
            return ScenarioCampaignCode::query()
                ->where('scenario_id', '=', $session->scenario_id)
                ->inRandomOrder()
                ->first();
        }

        $code = ScenarioCampaignCode::query()
            ->where('scenario_id', '=', $session->scenario_id)
            ->where('code', '=', $code)
            ->first();

        if (empty($code)) {
            throw ValidationException::withMessages([
                'code' => [__('Unknown code.')],
            ]);
        }
    }
}
