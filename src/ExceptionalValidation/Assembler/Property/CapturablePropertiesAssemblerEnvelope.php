<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Property;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableObject;
use ReflectionClass;

/**
 * @internal
 *
 * @template T of object
 */
final class CapturablePropertiesAssemblerEnvelope
{
    /** @param ReflectionClass<T> $reflectionClass */
    public function __construct(
        private readonly CapturableObject $captureObject,
        private readonly ReflectionClass $reflectionClass,
    ) {
    }

    public function getCaptureObject(): CapturableObject
    {
        return $this->captureObject;
    }

    /**
     * @internal
     *
     * @return ReflectionClass<T>
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }
}
