<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales\RequestVisit;

use App\Enums\Project\Location;
use App\Enums\Project\Status;
use App\Models\ScenarioClient;
use App\Models\ScenarioRequest;
use App\Traits\ListAndValidateClientForParticipant;
use App\Values\ProjectSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController
{
    use ListAndValidateClientForParticipant;

    public function view(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        $requests = $client->listAvailableRequestForGameSession($participant->session);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        return Inertia::render('game/sales/request-visit/client', [
            'client' => $client,
        ]);
    }

    public function store(Request $request, ScenarioClient $client)
    {
        $participant = $request->participant();

        $this->validateClientForParticipant($participant, $client);

        $requests = $client->listAvailableRequestForGameSession($participant->session);
        if ($requests->isEmpty()) {
            return redirect()->route('game.sales.request-visit.no-requests', [$client]);
        }

        $request->validate([
            'apply' => ['required', 'boolean'],
        ]);

        if (! $request->boolean('apply')) {
            return $this->createProjectAndRedirect($request, $client, $requests);
        }

        return redirect()->route('game.sales.request-visit.quiz', [$client]);
    }

    /**
     * @param Collection<array-key, ScenarioRequest> $requests
     */
    private function createProjectAndRedirect(Request $request, ScenarioClient $client, Collection $requests)
    {
        $participant = $request->participant();
        $projectRequest = $requests->first();

        $project = $participant->session->projects()
            ->create([
                'request_id' => $projectRequest->id,
                'client_id' => $client->id,
                'status' => Status::PENDING,
                'price' => $projectRequest->settings['value'] ?? 0,
                'failure_chance' => $projectRequest->settings['initialfailurechance'] ?? 0,
                'location' => Location::collect()->random(),
                'request_round_id' => $participant->session->currentRound->roundID,
                'settings' => ProjectSettings::fromArray([
                    'labConsultingApplied' => false,
                    'labConsultingIncluded' => false,
                ]),
            ]);

        return redirect()->route('game.sales.projects.view', [$project]);
    }
}
