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
                'label' => __('actions:marketing:campaign'),
                'href' => route('game.marketing.campaign.view'),
            ],
            [
                'label' => __('actions:marketing:training-broad'),
                'href' => route('game.marketing.training.view', [TrainingType::BROAD]),
            ],
            [
                'label' => __('actions:marketing:training-deep'),
                'href' => route('game.marketing.training.view', [TrainingType::DEEP]),
            ],
        ];

        if ($session->canRefreshHdma()) {
            $actions[] = [
                'label' => __('actions:marketing:hdma'),
                'href' => route('game.marketing.hdma.view'),
            ];
        }

        return Inertia::render('game/marketing/view', [
            'actions' => $actions,
        ]);
    }
}
