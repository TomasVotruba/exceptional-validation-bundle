<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList\Nested;

use PhPhD\ExceptionalValidation\Assembler\CaptureList\CaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectAssembler;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertyAssemblerEnvelope;
use ReflectionAttribute;
use Symfony\Component\Validator\Constraints\Valid;

/** @internal */
final class NestedObjectViaReflectionAssembler implements CaptureListAssembler
{
    public function __construct(
        private CapturableObjectAssembler $objectTreeAssembler,
    ) {
    }

    public function assembleCaptureItems(CapturablePropertyAssemblerEnvelope $propertyEnvelope): iterable
    {
        $capturableProperty = $propertyEnvelope->getCapturableProperty();

        $propertyValue = $capturableProperty->getValue();

        if (!is_object($propertyValue)) {
            return;
        }

        $reflectionProperty = $propertyEnvelope->getReflectionProperty();
        $validAttributes = $reflectionProperty->getAttributes(Valid::class, ReflectionAttribute::IS_INSTANCEOF);

        if ([] === $validAttributes) {
            return;
        }

        yield $this->objectTreeAssembler->assembleTree($propertyValue, $capturableProperty);
    }
}
