<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RequestVisit;

use App\Enums\Client\CarBrand;
use App\Enums\Client\Market;
use App\Enums\Client\YearsInBusiness;
use App\Enums\GameSession\TransactionType;
use App\Models\Project;
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
        $session = $participant->session;

        $this->validateClientForParticipant($participant, $client);

        $quizScore = $this->calculateQuizScore($request, $client);
        $includeLabConsulting = $session->settings->flow->enableLabConsultingForScore($quizScore);

        $requests = $client->listAvailableRequestForGameSession($session, $includeLabConsulting);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        $projectRequest = $requests->first();
        $projectRequest->load([
            'client',
        ]);

        $project = Project::fromRequest($projectRequest);

        $project->fill([
            'game_session_id' => $session->id,
            'request_round_id' => $session->currentRound->roundID,
            'settings' => ProjectSettings::fromArray([
                'labConsultingApplied' => true,
                'labConsultingIncluded' => $includeLabConsulting,
            ]),
        ])->save();

        $session->transactions()
            ->create([
                'participant_id' => $participant->id,
                'client_id' => $client->id,
                'project_id' => $project->id,
                'type' => TransactionType::LAB_CONSULTING,
                'round_id' => $session->currentRound->roundID,
                'value' => -5,
            ]);

        return redirect()->route('game.sales.projects.view', [$project]);
    }

    public function calculateQuizScore(Request $request, ScenarioClient $client)
    {
        $questions = $this->listQuestions();

        $correct = $questions
            ->map(function ($v, $key) use ($request, $client) {
                $givenAnswer = $request->input($key);
                if (empty($givenAnswer)) {
                    return 0;
                }

                $correctAnswer = $client->settings[$key];

                return ($correctAnswer === $givenAnswer) ? 1 : 0;
            })
            ->sum();

        return ($correct / $questions->count()) * 100;
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
