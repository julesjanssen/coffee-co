<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GameSession\Status;
use App\Models\GameSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class PruneGameSessions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        GameSession::query()
            ->where('status', '=', Status::CLOSED)
            ->where('updated_at', '<=', Date::now()->subDays(7)->startOfDay())
            ->get()
            ->each(fn($s) => $s->delete());
    }
}
