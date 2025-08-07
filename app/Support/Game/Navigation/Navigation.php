<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

use App\Enums\Participant\Role;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

abstract class Navigation implements Arrayable
{
    protected function __construct(
        protected Request $request,
        protected GameParticipant|GameFacilitator $auth,
    ) {}

    public static function forAuthenticatable(Request $request, GameParticipant|GameFacilitator $auth): self
    {
        if ($auth instanceof GameFacilitator) {
            return new Facilitator($request, $auth);
        }

        switch ($auth->role) {
            case Role::SALES_1:
            case Role::SALES_2:
            case Role::SALES_3:
            case Role::TECHNICAL_1:
            case Role::TECHNICAL_2:
                return new EmptyNav($request, $auth);

            case Role::MARKETING_1:
                return new Marketing($request, $auth);

            case Role::BACKOFFICE_1:
                return new BackOffice($request, $auth);

            default:
                throw new Exception('not implemented');
        }
    }

    /**
     * @return array<array-key, array{
     *     label: string,
     *     href: string,
     *     disabled?: bool,
     *     icon?: string
     * }>
     */
    abstract public function toArray(): array;
}
