<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Object;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableObject;
use ReflectionClass;

/** @internal */
final class CapturableObjectAssemblerEnvelope
{
    public function __construct(
        private CapturableObject $captureObject,
        private ReflectionClass $reflectionClass,
    ) {
    }

    public function getCaptureObject(): CapturableObject
    {
        return $this->captureObject;
    }

    /** @internal */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }
}
