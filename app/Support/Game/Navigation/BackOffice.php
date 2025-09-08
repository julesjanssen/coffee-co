<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class BackOffice extends Navigation
{
    public function listItems(): array
    {
        return [
            [
                'label' => __('navigation:backoffice:results'),
                'href' => route('game.backoffice.results'),
            ],
            [
                'disabled' => $this->session()->isPaused(),
                'label' => __('navigation:backoffice:projects'),
                'href' => route('game.backoffice.projects.index'),
            ],
        ];
    }
}
