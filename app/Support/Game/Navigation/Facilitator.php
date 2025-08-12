<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class Facilitator extends Navigation
{
    public function toArray(): array
    {
        return [
            [
                'disabled' => false,
                'label' => __('navigation:facilitator:status'),
                'href' => route('game.facilitator.status'),
            ],
            [
                'label' => __('navigation:facilitator:mmma'),
                'href' => route('game.facilitator.mmma'),
            ],
            [
                'label' => __('navigation:facilitator:results'),
                'href' => route('game.facilitator.results'),
            ],
            [
                'label' => __('navigation:facilitator:projects'),
                'href' => route('game.facilitator.projects'),
            ],
        ];
    }
}
