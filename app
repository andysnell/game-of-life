#!/usr/bin/env php
<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use WickedByte\App\Commands\GameOfLifeCommand;

require __DIR__ . '/vendor/autoload.php';

$app = new Application('Project Skeleton');
$app->addCommands([
    new GameOfLifeCommand(),
]);

$app->run();
