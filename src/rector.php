<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
    ])
    ->withSkip([
        '*Filter.php',
        '*Resource.php',
        __DIR__ . '/app/Actions/Fortify/CreateNewUser.php',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ]);
