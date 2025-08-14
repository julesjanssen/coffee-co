<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use RuntimeException;

class InvalidRoleException extends RuntimeException implements Responsable
{
    public function toResponse($request)
    {
        return redirect()->route('game.base');
    }
}
