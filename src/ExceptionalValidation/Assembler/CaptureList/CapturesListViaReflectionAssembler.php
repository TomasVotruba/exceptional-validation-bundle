<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\Tree\CaptureItem;
use PhPhD\ExceptionalValidation\Model\Tree\PropertyCaptureList;

/** @internal */
final class CapturesListViaReflectionAssembler implements CaptureListAssembler
{
    public function assembleCaptureItems(CaptureListAssemblerEnvelope $envelope): iterable
    {
        $captureList = $envelope
            ->getCapturableProperty()
            ->getCaptureList();

        $captureAttributes = $envelope
            ->getReflectionProperty()
            ->getAttributes(Capture::class);

        foreach ($captureAttributes as $captureAttribute) {
            $capture = $captureAttribute->newInstance();

            yield $this->createCaptureItem($captureList, $capture);
        }
    }

    private function createCaptureItem(PropertyCaptureList $captureList, Capture $capture): CaptureItem
    {
        return new CaptureItem($captureList, $capture->getExceptionClass(), $capture->getMessage());
    }
}
