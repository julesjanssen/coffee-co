<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

use App\Models\GameCampaignCode;

class Marketing extends Navigation
{
    public function listItems(): array
    {
        return [
            [
                'label' => __('navigation:marketing:results'),
                'href' => route('game.marketing.results'),
            ],
            [
                'label' => __('navigation:marketing:actions'),
                'href' => route('game.marketing.view'),
            ],
            [
                'disabled' => ! $this->hasInfoAvailable(),
                'label' => __('navigation:marketing:info'),
                'href' => route('game.marketing.info'),
            ],
        ];
    }

    private function hasInfoAvailable()
    {
        return GameCampaignCode::query()
            ->where('game_session_id', '=', $this->session()->id)
            ->exists();
    }
}
