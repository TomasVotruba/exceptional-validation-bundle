<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class PhdExceptionalValidationExtension extends Extension
{
    public const ALIAS = 'phd_exceptional_validation';

    /**
     * @param array<array-key,mixed> $configs
     *
     * @override
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO: Implement load() method.
    }

    /** @override */
    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
