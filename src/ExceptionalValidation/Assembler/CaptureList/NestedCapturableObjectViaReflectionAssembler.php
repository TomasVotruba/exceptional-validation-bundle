<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectAssembler;
use Symfony\Component\Validator\Constraints\Valid;

use function is_object;

/** @internal */
final class NestedCapturableObjectViaReflectionAssembler implements CaptureListAssembler
{
    public function __construct(
        private CapturableObjectAssembler $objectTreeAssembler,
    ) {
    }

    public function assembleCaptureItems(CaptureListAssemblerEnvelope $envelope): iterable
    {
        $capturableProperty = $envelope->getCapturableProperty();

        $propertyValue = $capturableProperty->getValue();

        if (!is_object($propertyValue)) {
            return;
        }

        $reflectionProperty = $envelope->getReflectionProperty();
        $validAttributes = $reflectionProperty->getAttributes(Valid::class);

        if ([] === $validAttributes) {
            return;
        }

        $nestedObject = $this->objectTreeAssembler->assembleTree($propertyValue, $capturableProperty);

        if (null === $nestedObject) {
            return;
        }

        yield $nestedObject;
    }
}
