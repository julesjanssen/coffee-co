<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class Materials extends Navigation
{
    public function toArray(): array
    {
        return [
            [
                'label' => __('navigation:materials:projects'),
                'href' => route('game.materials.projects'),
            ],
        ];
    }
}
