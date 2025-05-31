<?php

declare(strict_types=1);

namespace App\Support\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class JsExportable
{
    /**
     * @param string $name The name of the exported const
     */
    public function __construct(
        public ?string $name = null,
    ) {}
}
