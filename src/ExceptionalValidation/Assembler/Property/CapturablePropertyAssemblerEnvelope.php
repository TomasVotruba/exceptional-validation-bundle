<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Property;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;
use ReflectionProperty;

/** @internal */
final class CapturablePropertyAssemblerEnvelope
{
    public function __construct(
        private CapturableProperty $capturableProperty,
        private ReflectionProperty $reflectionProperty,
    ) {
    }

    public function getCapturableProperty(): CapturableProperty
    {
        return $this->capturableProperty;
    }

    /** @internal */
    public function getReflectionProperty(): ReflectionProperty
    {
        return $this->reflectionProperty;
    }
}
