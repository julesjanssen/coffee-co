<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class SalesScreen extends Navigation
{
    public function listItems(): array
    {
        return [
            [
                'disabled' => ! $this->session()->canDisplayResults(),
                'label' => __('navigation:sales-screen:results'),
                'href' => route('game.sales-screen.results'),
            ],
            [
                'label' => __('navigation:sales-screen:projects'),
                'href' => route('game.sales-screen.projects'),
            ],
        ];
    }
}
