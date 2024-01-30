<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;
use ReflectionProperty;

/** @internal */
final class CaptureListAssemblerEnvelope
{
    public function __construct(
        private readonly CapturableProperty $capturableProperty,
        private readonly ReflectionProperty $reflectionProperty,
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
