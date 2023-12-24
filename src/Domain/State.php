<?php

declare(strict_types=1);

namespace WickedByte\App\Domain;

enum State
{
    case LIVE;
    case DEAD;
}
