<?php

declare(strict_types=1);

namespace App\Http\Resources\Game;

use App\Models\GameFacilitator;
use Illuminate\Database\Eloquent\Model;

class GameAuthenticatableResource
{
    public static function make(Model $resource)
    {
        if ($resource instanceof GameFacilitator) {
            return GameFacilitatorResource::make($resource);
        }

        return GameParticipantResource::make($resource);
    }

    public static function collection($resource)
    {
        $first = collect($resource)->first();

        if ($first instanceof GameFacilitator) {
            return GameFacilitatorResource::collection($resource);
        }

        return GameParticipantResource::collection($resource);
    }
}
