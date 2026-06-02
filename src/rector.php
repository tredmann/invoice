<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPhpSets()
    ->withPaths([
        __DIR__ . '/app',
    ])
    ->withSkip([
        '*Filter.php',
        '*Resource.php',
        __DIR__ . '/app/Actions/Fortify/CreateNewUser.php',
    ])
    ->withAutoloadPaths([
        __DIR__ . '/rector/rules/',
    ])
    ->withSets([
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        LaravelLevelSetList::UP_TO_LARAVEL_130_WITHOUT_ATTRIBUTES,
    ]);
