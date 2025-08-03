<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Sales;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request)
    {
        $participant = $request->participant();

        $actions = [
            [
                'label' => 'sales relation visit',
                'href' => route('game.sales.relation-visit'),
            ],
            [
                'label' => 'sales request visit',
                'href' => route('game.sales.request-visit.view'),
            ],
        ];

        return Inertia::render('game/sales/view', [
            'actions' => $actions,
        ]);
    }
}
