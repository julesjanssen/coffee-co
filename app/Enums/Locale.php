<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;
use Illuminate\Support\Facades\App;
use Locale as LocaleClass;

enum Locale: string
{
    use EnumHelpers;

    case EN = 'en';

    case IT = 'it';

    case NL = 'nl';

    public function description(): string
    {
        return LocaleClass::getDisplayName($this->value, App::getLocale());
    }
}
