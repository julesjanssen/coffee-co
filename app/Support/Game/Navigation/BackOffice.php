<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class BackOffice extends Navigation
{
    public function toArray(): array
    {
        return [
            [
                'disabled' => false,
                'label' => __('navigation.backoffice.results'),
                'href' => route('game.backoffice.results'),
            ],
            [
                'disabled' => true,
                'label' => __('navigation.backoffice.projects'),
                'href' => route('game.backoffice.projects.index'),
            ],
        ];
    }
}
