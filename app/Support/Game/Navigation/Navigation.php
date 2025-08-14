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

            case Role::SALES_SCREEN:
                return new SalesScreen($request, $auth);

            case Role::TECHNICAL_SCREEN:
                return new TechnicalScreen($request, $auth);

            case Role::MARKETING_1:
                return new Marketing($request, $auth);

            case Role::BACKOFFICE_1:
                return new BackOffice($request, $auth);

            case Role::MATERIALS_1:
                return new Materials($request, $auth);

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
    abstract public function listItems(): array;

    /**
     * @return array<array-key, array{
     *     label: string,
     *     href: string,
     *     disabled?: bool,
     *     icon?: string,
     *     isActive: bool
     * }>
     */
    public function toArray(): array
    {
        $currentRoute = $this->request->route();
        $currentUrl = $currentRoute ? $currentRoute->uri() : '';

        return collect($this->listItems())->map(function (array $item) use ($currentUrl) {
            $item['isActive'] = $this->isActiveRoute($item['href'], $currentUrl);

            return $item;
        })->toArray();
    }

    protected function isActiveRoute(string $href, string $currentUrl): bool
    {
        // Extract the path from the href (remove domain/protocol if present)
        $itemPath = parse_url($href, PHP_URL_PATH) ?? $href;

        // Remove leading slash for comparison
        $itemPath = ltrim($itemPath, '/');
        $currentUrl = ltrim($currentUrl, '/');

        // Exact match or current route starts with item path
        return $currentUrl === $itemPath || str_starts_with($currentUrl, $itemPath . '/');
    }

    protected function session()
    {
        return $this->auth->session;
    }
}
