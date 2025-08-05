<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RequestVisit;

use App\Enums\Client\CarBrand;
use App\Enums\Client\Market;
use App\Enums\Client\YearsInBusiness;
use App\Enums\Project\Location;
use App\Enums\Project\Status;
use App\Models\ScenarioClient;
use App\Traits\ListAndValidateClientForParticipant;
use App\Values\ProjectSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        $questions = $this->listQuestions();

        return Inertia::render('game/sales/request-visit/quiz', [
            'client' => $client,
            'questions' => $questions,
        ]);
    }

    public function store(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        $quizScore = $this->calculateQuizScore($request, $client);
        $includeLabConsulting = $participant->session->settings->flow->enableLabConsultingForScore($quizScore);

        $requests = $client->listAvailableRequestForGameSession($participant->session, $includeLabConsulting);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        $projectRequest = $requests->first();

        $project = $participant->session->projects()
            ->create([
                'request_id' => $projectRequest->id,
                'client_id' => $client->id,
                'status' => Status::PENDING,
                'price' => $projectRequest->settings->value,
                'failure_chance' => $projectRequest->settings->initialfailurechance,
                'location' => Location::collect()->random(),
                'request_round_id' => $participant->session->currentRound->roundID,
                'settings' => ProjectSettings::fromArray([
                    'labConsultingApplied' => true,
                    'labConsultingIncluded' => $includeLabConsulting,
                ]),
            ]);

        return redirect()->route('game.sales.projects.view', [$project]);
    }

    public function calculateQuizScore(Request $request, ScenarioClient $client)
    {
        return random_int(0, 100);
    }

    private function listQuestions()
    {
        return collect([
            'market' => [
                'question' => __('In which market does this client operate?'),
                'options' => Market::asSelectArray(),
            ],
            'carbrand' => [
                'question' => __('What type of car does this client drive?'),
                'options' => CarBrand::asSelectArray(),
            ],
            'years' => [
                'question' => __('For how long has this client been in business?'),
                'options' => YearsInBusiness::asSelectArray(),
            ],
        ]);
    }
}
