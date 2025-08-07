<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing\Campaign;

use App\Models\GameCampaignCode;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CodeController
{
    public function view(Request $request, string $numbers, GameCampaignCode $code)
    {
        $participant = $request->participant();
        $session = $participant->session;

        if (! hash_equals((string) $code->code->code, (string) $numbers)) {
            throw new BadRequestHttpException();
        }

        return $code;
    }
}
