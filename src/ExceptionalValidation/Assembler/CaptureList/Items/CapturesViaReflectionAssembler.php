<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList\Items;

use PhPhD\ExceptionalValidation\Assembler\CaptureList\CaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertyAssemblerEnvelope;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\Tree\CaptureItem;
use ReflectionAttribute;

/** @internal */
final class CapturesViaReflectionAssembler implements CaptureListAssembler
{
    public function assembleCaptureItems(CapturablePropertyAssemblerEnvelope $propertyEnvelope): iterable
    {
        $captureList = $propertyEnvelope
            ->getCapturableProperty()
            ->getCaptureList();

        $captureAttributes = $propertyEnvelope
            ->getReflectionProperty()
            ->getAttributes(Capture::class, ReflectionAttribute::IS_INSTANCEOF);

        foreach ($captureAttributes as $captureAttribute) {
            $capture = $captureAttribute->newInstance();

            yield new CaptureItem($captureList, $capture);
        }
    }
}
