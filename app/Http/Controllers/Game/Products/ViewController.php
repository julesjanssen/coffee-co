<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Products;

use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use Illuminate\Http\Request;

class ViewController
{
    public function view(Request $request, string $product)
    {
        /** @var GameParticipant|GameFacilitator $user */
        $user = $request->user();
        $session = $user->session;

        $product = $session->scenario->products()
            ->where('public_id', '=', $product)
            ->firstOrFail();

        return [
            'id' => $product->public_id,
            'sqid' => $product->sqid,
            'title' => $product->title,
        ];
    }
}
