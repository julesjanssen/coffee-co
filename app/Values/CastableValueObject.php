<?php

declare(strict_types=1);

namespace App\Values;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class CastableValueObject implements Castable
{
    // limit properties to explicitly defined set
    public function __set(string $name, mixed $value): void
    {
        throw new InvalidArgumentException("Property '{$name}' is not defined");
    }

    public static function fromArray(array $array)
    {
        /** @phpstan-ignore new.static */
        $obj = new static();

        foreach ($array as $key => $value) {
            $obj->{$key} = $value;
        }

        return $obj;
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        $className = static::class;

        return new class ($className) implements CastsAttributes {
            public function __construct(
                protected string $className
            ) {}

            public function get(
                Model $model,
                string $key,
                mixed $value,
                array $attributes,
            ): ?object {
                if (! isset($attributes[$key])) {
                    return null;
                }

                $data = Json::decode($attributes[$key]);

                if (! is_array($data)) {
                    return null;
                }

                return $this->className::fromArray($data);
            }

            public function set(
                Model $model,
                string $key,
                mixed $value,
                array $attributes,
            ): array {
                return [$key => Json::encode($value)];
            }
        };
    }
}
