<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Disk;
use App\Enums\SystemTaskStatus;
use App\Jobs\ExecuteSystemTask;
use App\Support\SystemTasks\Contracts\SystemTaskRunner;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SystemTask extends Model
{
    use HasUuids;
    use UsesTenantConnection;

    protected $table = 'system_tasks';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'json',
        'result' => 'json',
        'status' => SystemTaskStatus::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'payload' => '[]',
        'result' => '[]',
        'status' => SystemTaskStatus::PENDING,
    ];

    public static function booted()
    {
        self::creating(function ($model) {
            if (empty($model->expires_at)) {
                $model->expires_at = Date::now()->addDay();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storage(): Filesystem
    {
        return Storage::build([
            'driver' => 'scoped',
            'disk' => Disk::SYSTEM_TASKS->value,
            'prefix' => $this->id,
        ]);
    }

    public static function dispatch(SystemTaskRunner $runner, array $payload = []): self
    {
        return DB::transaction(function () use ($runner, $payload) {
            $userID = Auth::id();

            $task = self::create([
                'user_id' => $userID,
                'payload' => $payload,
            ]);

            ExecuteSystemTask::dispatch($task, $runner::class);

            return $task;
        });
    }

    public function setStatus(SystemTaskStatus $status)
    {
        if ($this->status === $status) {
            return;
        }

        $this->status = $status;

        switch ($status) {
            case SystemTaskStatus::PROCESSING:
                $this->started_at = now();
                break;

            case SystemTaskStatus::COMPLETED:
            case SystemTaskStatus::FAILED:
                $this->finished_at = now();
                break;
        }

        $this->save();
    }

    public function isCompleted()
    {
        return $this->status === SystemTaskStatus::COMPLETED;
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'startedAt' => $this->started_at,
            'finishedAt' => $this->finished_at,
            'expiresAt' => $this->expires_at,
            'links' => array_filter([
                'view' => route('admin.system.tasks.view', [$this]),
                'download' => route('admin.system.tasks.download', [$this]),
                'result' => $this->result['url'] ?? null,
            ]),
        ];
    }
}
