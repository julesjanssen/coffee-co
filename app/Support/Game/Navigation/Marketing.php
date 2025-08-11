<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

use App\Models\GameCampaignCode;

class Marketing extends Navigation
{
    public function toArray(): array
    {
        return array_filter([
            [
                'label' => __('navigation:marketing:results'),
                'href' => route('game.marketing.results'),
            ],
            [
                'label' => __('navigation:marketing:actions'),
                'href' => route('game.marketing.view'),
            ],
            ($this->hasInfoAvailable() ? [
                'label' => __('navigation:marketing:info'),
                'href' => route('game.marketing.info'),
            ] : null),
        ]);
    }

    private function hasInfoAvailable()
    {
        return GameCampaignCode::query()
            ->where('game_session_id', '=', $this->session()->id)
            ->exists();
    }
}
