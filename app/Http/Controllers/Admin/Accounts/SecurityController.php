<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Resources\Admin\UserResource;
use App\Models\Passkey;
use App\Models\Tenant;
use App\Support\Login\IpInfo;
use App\Support\Login\UserAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class SecurityController
{
    public function view(Request $request)
    {
        return Inertia::render('accounts/security', [
            'account' => UserResource::make($request->user()),
            'passkeys' => $this->listPasskeys($request),
            'sessions' => $this->listSessions($request),
        ]);
    }

    private function listPasskeys(Request $request)
    {
        return Passkey::query()
            ->where('authenticatable_id', '=', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map($this->preparePasskeyDetails(...));
    }

    private function preparePasskeyDetails(Passkey $passkey)
    {
        return [
            'id' => hash('xxh3', $passkey->credential_id),
            'name' => $passkey->name,
            'createdAt' => $passkey->created_at,
            'lastUsedAt' => $passkey->last_used_at,
            'backupEligible' => $passkey->data->backupEligible,
            'transports' => $passkey->data->transports,
        ];
    }

    private function listSessions(Request $request)
    {
        if (Session::getDefaultDriver() !== 'database') {
            return;
        }

        $connection = DB::connection(config('session.connection'));

        return $connection
            ->table('sessions')
            ->where('tenant_id', '=', Tenant::current()->id)
            ->where('user_id', '=', $request->user()->id)
            ->get()
            ->map($this->prepareSessionDetails(...));
    }

    private function prepareSessionDetails(object $session)
    {
        return [
            'id' => hash('xxh3', $session->id),
            'lastActivity' => Date::createFromTimestamp($session->last_activity),
            'ip' => IpInfo::getDetails($session->ip_address),
            'userAgent' => UserAgent::getDetails($session->user_agent),
            'isCurrent' => $session->id === session()->getId(),
        ];
    }
}
