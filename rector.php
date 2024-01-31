<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([__DIR__.'/src']);

    $rectorConfig->rules([
        TypedPropertyFromAssignsRector::class,
        RemoveParentCallWithoutParentRector::class,
        PrivatizeFinalClassMethodRector::class,
    ]);
};
