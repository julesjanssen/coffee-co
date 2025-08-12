<?php

declare(strict_types=1);

namespace App\Values;

use App\Models\Scenario;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Date;

class GameRound implements Arrayable
{
    public const ROUNDS_PER_YEAR = 12;

    /**
     * @return array<array-key, int>
     */
    public static function getRangeForYear(int $year): array
    {
        $min = ($year - 1) * self::ROUNDS_PER_YEAR + 1;

        return range($min, $min + (self::ROUNDS_PER_YEAR - 1));
    }

    public function __construct(
        public readonly Scenario $scenario,
        public readonly int $roundID,
    ) {}

    public function isFirstRound()
    {
        return $this->roundID === 1;
    }

    public function isLastRound()
    {
        return $this->roundID === $this->scenario->numberOfRounds();
    }

    public function isFirstRoundOfYear()
    {
        return $this->roundID % self::ROUNDS_PER_YEAR === 1;
    }

    public function isLastRoundOfYear()
    {
        return $this->roundID % self::ROUNDS_PER_YEAR === 0;
    }

    public function isFirstRoundOfQuarter()
    {
        return $this->roundID % (self::ROUNDS_PER_YEAR / 4) === 1;
    }

    public function isLastRoundOfQuarter()
    {
        return $this->roundID % (self::ROUNDS_PER_YEAR / 4) === 0;
    }

    public function month()
    {
        $value = $this->roundID % self::ROUNDS_PER_YEAR ?: self::ROUNDS_PER_YEAR;

        if ($value < 0) {
            return self::ROUNDS_PER_YEAR - abs($value);
        }

        return $value;
    }

    public function year()
    {
        return (int) ceil($this->roundID / self::ROUNDS_PER_YEAR);
    }

    public function displayYear()
    {
        $startYear = $this->scenario->settings->startYear ;

        return $startYear + $this->year() - 1;
    }

    public function quarter()
    {
        return (int) ((($this->roundID - 1) % self::ROUNDS_PER_YEAR) / 3) + 1;
    }

    public function previous()
    {
        if ($this->roundID <= 1) {
            return;
        }

        return new self($this->scenario, ($this->roundID - 1));
    }

    public function next()
    {
        if ($this->isLastRound()) {
            return;
        }

        return new self($this->scenario, ($this->roundID + 1));
    }

    public function addRounds(int $number)
    {
        return new self($this->scenario, ($this->roundID + $number));
    }

    public function displayFull()
    {
        return $this->getDateObject()->isoFormat('MMMM YYYY');
    }

    public function display()
    {
        return $this->getDateObject()->isoFormat('MMM YYYY');
    }

    public function toArray()
    {
        return [
            'display' => $this->display(),
            'displayFull' => $this->displayFull(),
            'isLastRoundOfYear' => $this->isLastRoundOfYear(),
        ];
    }

    private function getDateObject()
    {
        return Date::createFromDate($this->displayYear(), $this->month(), 1);
    }
}
