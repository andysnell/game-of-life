<?php

declare(strict_types=1);

namespace WickedByte\App\Domain;

class Grid
{
    /**
     * @var array<array<Cell>>
     */
    private array $cells = [];

    public function __construct()
    {
    }

    public static function make(): self
    {
        return new self();
    }

    public function cell(int $x, int $y): Cell
    {
        return $this->cells[$x][$y] ?? new Cell($x, $y);
    }

    /**
     * @return array<Cell>
     */
    public function cells(bool $filter = false): array
    {
        $cells = [];
        foreach ($this->cells as $row) {
            foreach ($row as $cell) {
                if ($filter && $cell->state === State::DEAD) {
                    continue;
                }

                $cells[] = $cell;
            }
        }

        return $cells;
    }

    /**
     * We only need to look at living cells and dead cells that have living
     * neighbors, so when we add a cell to the grid, we also add its neighbors.
     */
    public function add(Cell $cell): static
    {
        $this->cells[$cell->x][$cell->y] = $cell;
        for ($x = $cell->x - 1; $x <= $cell->x + 1; ++$x) {
            for ($y = $cell->y - 1; $y <= $cell->y + 1; ++$y) {
                $this->cells[$x][$y] ??= new Cell($x, $y);
            }
        }

        return $this;
    }

    /**
     * Get the 8 immediate neighbors of the cell passed as an argument.
     *
     * @return array<Cell>
     */
    public function neighbors(Cell $cell): array
    {
        $neighbors = [];
        for ($x = $cell->x - 1; $x <= $cell->x + 1; ++$x) {
            for ($y = $cell->y - 1; $y <= $cell->y + 1; ++$y) {
                if ($x === $cell->x && $y === $cell->y) {
                    continue;
                }

                $neighbors[] = $this->cell($x, $y);
            }
        }

        return $neighbors;
    }
}
