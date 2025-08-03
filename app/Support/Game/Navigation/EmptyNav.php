<?php

declare(strict_types=1);

namespace App\Support\Game\Navigation;

class EmptyNav extends Navigation
{
    public function toArray(): array
    {
        return [];
    }
}
