<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class BackOffice extends Navigation
{
    public function toArray(): array
    {
        return [
            [
                'label' => __('navigation:backoffice:results'),
                'href' => route('game.backoffice.results'),
            ],
            [
                'label' => __('navigation:backoffice:projects'),
                'href' => route('game.backoffice.projects.index'),
            ],
        ];
    }
}
