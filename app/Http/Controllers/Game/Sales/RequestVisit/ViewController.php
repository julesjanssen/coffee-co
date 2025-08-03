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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ViewController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request)
    {
        $participant = $request->participant();

        $clients = $this->listClientsForParticipant($participant)
            ->map(fn($client) => [
                'title' => $client->title,
                'href' => route('game.sales.request-visit.client', [$client->sqid]),
            ]);

        return Inertia::render('game/sales/request-visit/view', [
            'clients' => $clients,
        ]);
    }

    /*
    public function client(Request $request, ScenarioClient $client)
    {
        $this->validateClient($request, $client);

        $participant = $request->participant();

        $requests = $client->listAvailableRequestForGameSession($participant->session);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'apply' => ['required', 'boolean'],
            ]);

            if (! $request->boolean('apply')) {
                return redirect()->route('game.sales.request-visit.show-request', [$client]);
            }

            return redirect()->route('game.sales.request-visit.quiz', [$client]);
        }

        return Inertia::render('game/sales/request-visit/client', [
            'client' => $client,
        ]);
    }

    public function quiz(Request $request, ScenarioClient $client)
    {
        $this->validateClient($request, $client);

        $questions = collect([
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

        if ($request->isMethod('post')) {
            $request->validate([
                'answers' => ['required', 'array', 'size:'. $questions->count()],
            ]);

            // TODO: apply costs
            // TODO: check answers
            $request->session()->put('apply-labconsulting', true);
            $request->session()->put('quiz-score', random_int(0, 100));

            return redirect()->route('game.sales.request-visit.show-request', [$client]);
        }

        return Inertia::render('game/sales/request-visit/quiz', [
            'client' => $client,
            'questions' => $questions,
        ]);
    }

    public function showRequest(Request $request, ScenarioClient $client)
    {
        $this->validateClient($request, $client);
        $participant = $request->participant();

        $quizScore = $request->session()->get('quiz-score');
        $applyLabConsulting = (bool) $request->session()->get('apply-labconsulting');
        $includeLabConsulting = false;

        if ($applyLabConsulting) {
            $includeLabConsulting = $participant->session->settings->flow->enableLabConsultingForScore($quizScore);
        }

        $requests = $client->listAvailableRequestForGameSession($participant->session, $includeLabConsulting);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        $projectRequest = $requests->first();

        $participant->session->projects()
            ->create([
                'request_id' => $projectRequest->id,
                'client_id' => $client->id,
                'status' => Status::PENDING,
                'price' => $projectRequest->settings['value'] ?? 0,
                'failure_chance' => $projectRequest->settings['initialfailurechance'] ?? 0,
                'location' => Location::collect()->random(),
                'request_round_id' => $participant->session->currentRound->roundID,
            ]);

        return Inertia::render('game/sales/request-visit/show-request', [
            'client' => $client,
            'applyLabConsulting' => $applyLabConsulting,
            'includeLabConsulting' => $includeLabConsulting,
            'projectRequest' => [
                'description' => $projectRequest->description,
                'value' => $projectRequest->data['value'],
            ],
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }

    public function noRequests(Request $request, ScenarioClient $client)
    {
        $this->validateClient($request, $client);

        $participant = $request->participant();
        $requests = $client->listAvailableRequestForGameSession($participant->session);
        if ($requests->isNotEmpty()) {
            return redirect()->route('game.sales.request-visit.client', [$client]);
        }

        $hasMaxProjectsForCurrentYear = $client->hasMaxProjectsForCurrentYear($participant->session);

        return Inertia::render('game/sales/request-visit/no-requests', [
            'client' => $client,
            'hasMaxProjectsForCurrentYear' => $hasMaxProjectsForCurrentYear,
            'links' => [
                'back' => route('game.sales.view'),
            ],
        ]);
    }

    private function validateClient(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();
        $clients = $this->listClients($participant);

        if (! $clients->contains($client)) {
            throw new BadRequestHttpException;
        }
    }
    */
}
