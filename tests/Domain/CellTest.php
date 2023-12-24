<?php

declare(strict_types=1);

namespace WickedByte\Tests\App\Domain;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WickedByte\App\Domain\Cell;
use WickedByte\App\Domain\State;

class CellTest extends TestCase
{
    #[Test]
    public function itCanBeCreatedAsLive(): void
    {
        $cell = Cell::live(1, 2);

        self::assertSame(1, $cell->x);
        self::assertSame(2, $cell->y);
        self::assertSame(State::LIVE, $cell->state);
    }
}
