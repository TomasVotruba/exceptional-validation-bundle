<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertyAssemblerEnvelope;

/** @internal */
final class CompositeCaptureListAssembler implements CaptureListAssembler
{
    public function __construct(
        /** @var iterable<CaptureListAssembler> */
        private iterable $captureListAssemblers,
    ) {
    }

    public function assembleCaptureItems(CapturablePropertyAssemblerEnvelope $propertyEnvelope): iterable
    {
        foreach ($this->captureListAssemblers as $itemsAssembler) {
            yield from $itemsAssembler->assembleCaptureItems($propertyEnvelope);
        }
    }
}
