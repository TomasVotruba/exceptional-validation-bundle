<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Property;

use ArrayIterator;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectAssemblerEnvelope;
use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;
use ReflectionProperty;

/** @internal */
final class CapturablePropertiesViaReflectionAssembler implements CapturablePropertiesAssembler
{
    public function __construct(
        private CaptureListAssembler $captureListAssembler,
    ) {
    }

    public function assembleProperties(CapturableObjectAssemblerEnvelope $objectEnvelope): iterable
    {
        $reflectionClass = $objectEnvelope->getReflectionClass();
        $captureObject = $objectEnvelope->getCaptureObject();

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $name = $reflectionProperty->getName();
            $value = $this->getPropertyValue($captureObject->getObject(), $reflectionProperty);

            $captureList = new ArrayIterator();
            $property = new CapturableProperty($captureObject, $name, $value, $captureList);
            $propertyEnvelope = new CapturablePropertyAssemblerEnvelope($property, $reflectionProperty);

            foreach ($this->captureListAssembler->assembleCaptureItems($propertyEnvelope) as $item) {
                $captureList->append($item);
            }

            if ($captureList->count() === 0) {
                continue;
            }

            yield $property;
        }
    }

    private function getPropertyValue(object $message, ReflectionProperty $property): mixed
    {
        if (!$property->isInitialized($message)) {
            $propertyValue = null;
        } else {
            $propertyValue = $property->getValue($message);
        }

        return $propertyValue;
    }
}
