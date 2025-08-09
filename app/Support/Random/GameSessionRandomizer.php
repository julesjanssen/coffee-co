<?php

declare(strict_types=1);

namespace App\Support\Random;

use App\Models\GameSession;
use Illuminate\Support\Traits\ForwardsCalls;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class GameSessionRandomizer
{
    use ForwardsCalls;

    private function __construct(
        private Randomizer $randomizer,
    ) {}

    public static function forGameSession(GameSession $session)
    {
        $seed = hash('xxh3', 'session:' . (string) $session->id);

        $engine = new Xoshiro256StarStar(
            hash('sha256', $seed, binary: true)
        );

        return new self(new Randomizer($engine))    ;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->randomizer, $method, $parameters);
    }
}
