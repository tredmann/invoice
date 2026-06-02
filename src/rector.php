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
        // Produces incorrect syntax for dynamic instantiation: (new $var())->method() → new $var()->method()
        \Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector::class,
        // Incorrectly resolves string view names to class constants (e.g. 'auth.login' → Login::class)
        __DIR__ . '/app/Providers/FortifyServiceProvider.php',
        // Drops the abstract key from singleton(), breaking IoC resolution by class name
        \RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector::class,
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
