<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical\Maintenance\Projects;

use App\Enums\GameSession\ScoreType;
use App\Http\Resources\Game\ProjectActionResource;
use App\Models\GameScore;
use App\Models\Project;
use App\Models\ProjectAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExtraServiceController
{
    public function store(Request $request, Project $project, ProjectAction $action)
    {
        if (! $action->project->is($project)) {
            throw new NotFoundHttpException();
        }

        if (! empty($action->details->hint)) {
            return ProjectActionResource::make($action);
        }

        $participant = $request->participant();
        $session = $participant->session;

        $client = $project->client;

        GameScore::firstOrCreate([
            'game_session_id' => $session->id,
            'participant_id' => $participant->id,
            'client_id' => $client->id,
            'type' => ScoreType::NPS,
            'trigger_type' => $action->getMorphClass(),
            'trigger_id' => $action->getKey(),
            'event' => 'extra-service',
            'round_id' => $session->currentRound->roundID,
            'value' => 2,
        ]);

        $hints = collect([
            __(':name operates in the :market market.', ['name' => $client->title, 'market' => $client->market->description()]),
            __('The owner of :name drives a :car.', ['name' => $client->title, 'car' => $client->carBrand->description()]),
            __(':name has been in business for :num years.', ['name' => $client->title, 'num' => $client->yearsInBusiness->years()]),
        ]);

        $action->details->extraService = true;
        $action->details->hint = $hints->random();
        $action->save();

        return ProjectActionResource::make($action);
    }
}
