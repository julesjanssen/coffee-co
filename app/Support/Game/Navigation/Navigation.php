<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class Navigation implements Arrayable
{
    protected function __construct(
        protected Request $request,
        protected GameParticipant|GameFacilitator $auth,
    ) {}

    public static function forAuthenticatable(GameParticipant|GameFacilitator $auth): self
    {
        return new self(request(), $auth);
    }

    /**
     * @return array<array-key, array{
     *     label: string,
     *     href: string,
     *     disabled?: bool,
     *     icon?: string
     * }>
     */
    public function toArray()
    {
        return [
            [
                'disabled' => false,
                'label' => __('navigation:status'),
                'href' => route('game.facilitator.status'),
            ],
            [
                'disabled' => true,
                'label' => __('navigation:mmma'),
                'href' => route('game.facilitator.mmma'),
            ],
        ];
    }
}
