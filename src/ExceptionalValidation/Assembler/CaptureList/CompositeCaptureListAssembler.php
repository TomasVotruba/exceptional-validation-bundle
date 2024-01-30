<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

/** @internal */
final class CompositeCaptureListAssembler implements CaptureListAssembler
{
    public function __construct(
        /** @var iterable<CaptureListAssembler> */
        private readonly iterable $captureListAssemblers,
    ) {
    }

    public function assembleCaptureItems(CaptureListAssemblerEnvelope $envelope): iterable
    {
        foreach ($this->captureListAssemblers as $captureListAssembler) {
            yield from $captureListAssembler->assembleCaptureItems($envelope);
        }
    }
}
