<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class Marketing extends Navigation
{
    public function toArray(): array
    {
        // TODO: check if info is available
        $hasInfo = random_int(0, 100) > 50;

        return array_filter([
            [
                'label' => __('navigation:marketing:results'),
                'href' => route('game.marketing.results'),
            ],
            [
                'label' => __('navigation:marketing:actions'),
                'href' => route('game.marketing.view'),
            ],
            ($hasInfo ? [
                'label' => __('navigation:marketing:info'),
                'href' => route('game.marketing.info'),
            ] : null),
        ]);
    }
}
