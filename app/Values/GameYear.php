<?php

declare(strict_types=1);

namespace App\Values;

use App\Models\Scenario;
use Illuminate\Support\Facades\Date;

class GameYear
{
    public function __construct(
        public readonly Scenario $scenario,
        public readonly int $yearID,
    ) {}

    public function isFirstYear()
    {
        return $this->yearID === 1;
    }

    public function isLastYear()
    {
        return $this->yearID === $this->scenario->years();
    }

    public function previous()
    {
        if ($this->yearID <= 1) {
            return;
        }

        return new self($this->scenario, ($this->yearID - 1));
    }

    public function next()
    {
        if ($this->isLastYear()) {
            return;
        }

        return new self($this->scenario, ($this->yearID + 1));
    }

    public function minRoundID()
    {
        return ($this->yearID - 1) * GameRound::ROUNDS_PER_YEAR + 1;
    }

    public function maxRoundID()
    {
        return $this->minRoundID() + (GameRound::ROUNDS_PER_YEAR - 1);
    }

    /**
     * @return array<array-key, int>
     */
    public function roundIDs()
    {
        return range($this->minRoundID(), $this->maxRoundID());
    }

    /**
     * @return array<array-key, GameRound>
     */
    public function rounds()
    {
        return collect($this->roundIDs())
            ->map(fn($i) => new GameRound($this->scenario, $i))
            ->all();
    }

    public function display()
    {
        return $this->getDateObject()->isoFormat('YY');
    }

    public function toArray()
    {
        return [
            'id' => $this->yearID,
            'display' => $this->display(),
        ];
    }

    private function getDateObject()
    {
        $startYear = $this->scenario->settings->startYear;
        $year = $startYear + $this->yearID - 1;

        return Date::createFromDate($year, 1, 1);
    }
}
