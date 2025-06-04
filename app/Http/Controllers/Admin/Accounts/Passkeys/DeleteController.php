<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounts\Passkeys;

use App\Models\Passkey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DeleteController
{
    public function delete(Request $request, Passkey $passkey)
    {
        $user = $request->user();

        if ($passkey->authenticatable_id !== $user->id) {
            throw new BadRequestException();
        }

        $passkey->delete();

        return response()->noContent();
    }
}
