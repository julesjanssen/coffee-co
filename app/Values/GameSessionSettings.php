<?php

declare(strict_types=1);

namespace App\Values;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class GameSessionSettings implements Castable
{
    public bool $shouldPauseAfterCurrentRound = false;

    public int $failChanceIncreasePerRound = 2;

    public int $maxProjectsPerClientPerYear = 2;

    public int $secondsPerRound = 60;

    public int $roundsToSubmitOffer = 10;

    public int $roundsToDeliverProject = 6;

    public int $costLabconsultingRequestVisit = 5;

    public int $costLabconsultingOffer = 25;

    public int $clientNpsStart {
        set(?int $clientNpsStart) {
            if (is_null($clientNpsStart)) {
                $clientNpsStart = 60;
            }

            if ($clientNpsStart < 0 || $clientNpsStart > 100) {
                throw new InvalidArgumentException('Client NPS start should be between 0 & 100');
            }

            $this->clientNpsStart = $clientNpsStart;
        }
    }

    public int $startYear {
        get => $this->startYear ?? (int) date('Y') + 1;
    }

    // limit properties to explicitly defined set
    public function __set(string $name, mixed $value): void
    {
        throw new InvalidArgumentException("Property '{$name}' is not defined");
    }

    public static function fromArray(array $array)
    {
        $obj = new self();

        foreach ($array as $key => $value) {
            $obj->{$key} = $value;
        }

        return $obj;
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes {
            public function get(
                Model $model,
                string $key,
                mixed $value,
                array $attributes,
            ): ?GameSessionSettings {
                if (! isset($attributes[$key])) {
                    return null;
                }

                $data = Json::decode($attributes[$key]);

                if (! is_array($data)) {
                    return null;
                }

                return GameSessionSettings::fromArray($data);
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
