<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\Scenario\Status;
use App\Models\Scenario;
use App\Models\ScenarioRequestSolution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PruneScenarios implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        Scenario::query()
            ->where('status', '=', Status::ARCHIVED)
            ->whereNotExists(function ($query) {
                $query->from('game_sessions as gs')
                    ->whereColumn('gs.scenario_id', '=', 'scenarios.id');
            })
            ->limit(20)
            ->get()
            ->each($this->pruneScenario(...));
    }

    private function pruneScenario(Scenario $scenario)
    {
        $scenario->load([
            'clients',
            'clients.attachments',
        ]);

        // remove per client because of attachments
        $scenario->clients->each(fn($client) => $client->delete());

        // not directly related
        ScenarioRequestSolution::query()
            ->where('scenario_id', '=', $scenario->id)
            ->delete();

        $scenario->requests()->delete();
        $scenario->products()->delete();
        $scenario->campaignCodes()->delete();
        $scenario->tips()->delete();

        $scenario->delete();
    }
}
