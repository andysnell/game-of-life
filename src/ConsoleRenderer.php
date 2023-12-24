<?php

declare(strict_types=1);

namespace WickedByte\App;

use WickedByte\App\Domain\Grid;
use WickedByte\App\Domain\State;

readonly class ConsoleRenderer
{
    public function __construct(
        public int $min_x = 0,
        public int $max_x = 79,
        public int $min_y = 0,
        public int $max_y = 39,
    ) {
    }

    public function render(Grid $grid): string
    {
        $output = '';
        for ($y = $this->max_y; $y >= $this->min_y; --$y) {
            for ($x = $this->min_x; $x <= $this->max_x; ++$x) {
                $output .= $grid->cell($x, $y)->state === State::LIVE ? 'L' : 'D';
            }

            $output .= "\n";
        }

        return $output;
    }

    public function size(): int
    {
        return ($this->max_x - $this->min_x + 1) * ($this->max_y - $this->min_y + 1);
    }
}
