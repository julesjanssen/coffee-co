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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SecurityController
{
    public function view(Request $request)
    {
        return Inertia::render('accounts/security', [
            'account' => UserResource::make($request->user()),
            'passkeys' => $this->listPasskeys($request),
            'sessions' => $this->listMappedSessions($request),
        ]);
    }

    public function sessionRevoke(Request $request, string $session)
    {
        $session = $this->listSessions($request)
            ->firstWhere(fn($v) => $this->hashSessionID($v->id) === $session);

        if (empty($session)) {
            throw new BadRequestHttpException();
        }

        $this->sessionConnection()
            ->table('sessions')
            ->where('user_id', '=', $request->user()->id)
            ->where('id', '=', $session->id)
            ->limit(1)
            ->delete();

        return response()->noContent();
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
            'id' => $passkey->sqid,
            'name' => $passkey->name,
            'createdAt' => $passkey->created_at,
            'lastUsedAt' => $passkey->last_used_at,
            'backupEligible' => $passkey->data->backupEligible,
            'transports' => $passkey->data->transports,
            'links' => [
                'delete' => route('admin.account.passkeys.delete', [$passkey]),
            ],
        ];
    }

    private function listMappedSessions(Request $request)
    {
        return $this->listSessions($request)
            ->map($this->prepareSessionDetails(...));
    }

    private function listSessions(Request $request)
    {
        if (Session::getDefaultDriver() !== 'database') {
            return collect([]);
        }

        return $this->sessionConnection()
            ->table('sessions')
            ->where('tenant_id', '=', Tenant::current()->id)
            ->where('user_id', '=', $request->user()->id)
            ->get();
    }

    private function prepareSessionDetails(object $session)
    {
        $sessionID = $this->hashSessionID($session->id);

        return [
            'id' => $sessionID,
            'lastActivity' => Date::createFromTimestamp($session->last_activity),
            'ip' => IpInfo::getDetails($session->ip_address),
            'userAgent' => UserAgent::getDetails($session->user_agent),
            'isCurrent' => $session->id === session()->getId(),
            'links' => [
                'delete' => route('admin.account.security.sessions.delete', [$sessionID]),
            ],
        ];
    }

    private function sessionConnection()
    {
        return DB::connection(config('session.connection'));
    }

    private function hashSessionID($sessionID)
    {
        return hash('xxh3', (string) $sessionID);
    }
}
