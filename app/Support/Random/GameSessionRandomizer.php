<?php

declare(strict_types=1);

namespace App\Support\Random;

use App\Models\GameSession;
use Illuminate\Support\Traits\ForwardsCalls;
use Random\Engine\Xoshiro256StarStar;
use Random\IntervalBoundary;
use Random\Randomizer;

/**
 * @method void shuffleArray(array &$array)
 * @method int getInt(int $min, int $max)
 * @method float getFloat(float $min, float $max, IntervalBoundary $boundary = \Random\IntervalBoundary::ClosedOpen)
 * @method string getBytes(int $length)
 * @method array pickArrayKeys(array $array, int $num)
 * @method mixed pickArrayKeyValue(array $array)
 * @method string shuffleString(string $string)
 * @method array shuffleArray(array $array)
 */
class GameSessionRandomizer
{
    use ForwardsCalls;

    private function __construct(
        private Randomizer $randomizer,
    ) {}

    public static function forGameSession(GameSession $session)
    {
        $seed = 'session:' . (string) $session->id;

        $engine = new Xoshiro256StarStar(
            hash('sha256', $seed, binary: true)
        );

        return new self(new Randomizer($engine))    ;
    }

    public function splitItemsRandomly(array $items, int $parts)
    {
        $itemCount = count($items);
        $shuffled = $this->shuffleArray($items);

        $baseSize = intval($itemCount / $parts);
        $remainder = $itemCount % $parts;

        // Create array of part sizes
        $partSizes = array_fill(0, $parts, $baseSize);

        // Distribute the remainder randomly among parts
        $remainderIndices = $this->shuffleArray(range(0, $parts - 1));

        for ($i = 0; $i < $remainder; $i++) {
            $partSizes[$remainderIndices[$i]]++;
        }

        // Split the shuffled items into parts
        $result = [];
        $currentIndex = 0;

        for ($i = 0; $i < $parts; $i++) {
            $result[] = array_slice($shuffled, $currentIndex, $partSizes[$i]);
            $currentIndex += $partSizes[$i];
        }

        return $result;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->randomizer, $method, $parameters);
    }
}
