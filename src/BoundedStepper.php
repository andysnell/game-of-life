<?php

declare(strict_types=1);

namespace WickedByte\App;

use WickedByte\App\Domain\Cell;
use WickedByte\App\Domain\Grid;
use WickedByte\App\Domain\State;

/**
 * While the classic Game of Life is played on an infinite grid, we are not going
 * to display an infinite grid on the screen. This means we don't need to track
 * cells that are outside the bounds of the screen. We can use this class to
 * limit the cells we track to a bounded area as we step through generations.
 */
readonly class BoundedStepper
{
    public function __construct(
        public int $min_x = 0,
        public int $max_x = 79,
        public int $min_y = 0,
        public int $max_y = 39,
    ) {
    }

    public function tick(Grid $grid, Grid $next = new Grid()): Grid
    {
        foreach ($grid->cells() as $cell) {
            if ($cell->x > $this->max_x || $cell->x < $this->min_x) {
                continue;
            }

            if ($cell->y > $this->max_y || $cell->y < $this->min_y) {
                continue;
            }

            $live_neighbors = \count(\array_filter(
                $grid->neighbors($cell),
                static fn(Cell $cell): bool => $cell->state === State::LIVE,
            ));

            if ($live_neighbors === 3 || ($cell->state === State::LIVE && $live_neighbors === 2)) {
                $next->add(Cell::live($cell->x, $cell->y));
            }
        }

        return $next;
    }
}
