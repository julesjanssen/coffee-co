<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class TechnicalScreen extends Navigation
{
    public function listItems(): array
    {
        return [
            [
                'disabled' => ! $this->session()->canDisplayResults(),
                'label' => __('navigation:technical-screen:results'),
                'href' => route('game.technical-screen.results'),
            ],
            [
                'label' => __('navigation:technical-screen:projects'),
                'href' => route('game.technical-screen.projects'),
            ],
        ];
    }
}
