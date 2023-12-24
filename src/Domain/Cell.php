<?php

declare(strict_types=1);

namespace WickedByte\App\Domain;

final readonly class Cell
{
    public function __construct(
        public int $x,
        public int $y,
        public State $state = State::DEAD,
    ) {
    }

    public static function live(int $x, int $y): self
    {
        return new self($x, $y, State::LIVE);
    }
}
