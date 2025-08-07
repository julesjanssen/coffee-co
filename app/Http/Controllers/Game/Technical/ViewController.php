<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Technical;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $actions = [
            [
                'label' => __('navigation:technical:maintenance-visit'),
                'href' => route('game.technical.maintenance.view'),
            ],
            [
                'label' => __('navigation:technical:installation-visit'),
                'href' => route('game.technical.installation.view'),
            ],
        ];

        return Inertia::render('game/technical/view', [
            'actions' => $actions,
        ]);
    }
}
