<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing;

use App\Enums\GameSession\TrainingType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        $actions = [
            [
                'label' => __('navigation:marketing:campaign'),
                'href' => route('game.marketing.campaign.view'),
            ],
            [
                'label' => __('navigation:marketing:training-broad'),
                'href' => route('game.marketing.training.view', [TrainingType::BROAD]),
            ],
            [
                'label' => __('navigation:marketing:training-deep'),
                'href' => route('game.marketing.training.view', [TrainingType::DEEP]),
            ],
        ];

        if ($session->canRefreshMmma()) {
            $actions[] = [
                'label' => __('navigation:marketing:mmma'),
                'href' => route('game.marketing.mmma.view'),
            ];
        }

        return Inertia::render('game/marketing/view', [
            'actions' => $actions,
        ]);
    }
}
