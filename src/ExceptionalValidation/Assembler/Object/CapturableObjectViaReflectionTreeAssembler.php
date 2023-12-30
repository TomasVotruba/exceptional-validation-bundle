<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Object;

use ArrayIterator;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertiesAssembler;
use PhPhD\ExceptionalValidation\CaptureTreeAssembler;
use PhPhD\ExceptionalValidation\Model\Tree\CapturableObject;
use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;
use ReflectionAttribute;
use ReflectionClass;

/** @internal */
final class CapturableObjectViaReflectionTreeAssembler implements CapturableObjectAssembler, CaptureTreeAssembler
{
    public function __construct(
        private CapturablePropertiesAssembler $propertiesAssembler,
    ) {
    }

    public function assembleTree(object $message, ?CapturableProperty $parent = null): ?CapturableObject
    {
        $reflectionClass = new ReflectionClass($message);

        if ([] === $reflectionClass->getAttributes(ExceptionalValidation::class, ReflectionAttribute::IS_INSTANCEOF)) {
            return null;
        }

        $capturableProperties = new ArrayIterator();
        $capturableObject = new CapturableObject($message, $parent?->getCaptureList(), $capturableProperties);
        $envelope = new CapturableObjectAssemblerEnvelope($capturableObject, $reflectionClass);

        foreach ($this->propertiesAssembler->assembleProperties($envelope) as $property) {
            $capturableProperties->append($property);
        }

        if (0 === count($capturableProperties)) {
            return null;
        }

        return $capturableObject;
    }
}
