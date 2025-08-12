<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

use App\Enums\GameSession\Status;

class Facilitator extends Navigation
{
    public function toArray(): array
    {
        $sessionIsPending = $this->session()->status->is(Status::PENDING);

        return [
            [
                'label' => __('navigation:facilitator:status'),
                'href' => route('game.facilitator.status'),
            ],
            [
                'disabled' => $sessionIsPending,
                'label' => __('navigation:facilitator:hdma'),
                'href' => route('game.facilitator.hdma'),
            ],
            [
                'disabled' => $sessionIsPending,
                'label' => __('navigation:facilitator:results'),
                'href' => route('game.facilitator.results'),
            ],
            [
                'disabled' => $sessionIsPending,
                'label' => __('navigation:facilitator:projects'),
                'href' => route('game.facilitator.projects'),
            ],
        ];
    }
}
