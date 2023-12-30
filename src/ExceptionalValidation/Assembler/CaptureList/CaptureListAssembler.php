<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertyAssemblerEnvelope;
use PhPhD\ExceptionalValidation\Model\CaptureTree;

/** @internal */
interface CaptureListAssembler
{
    /** @return iterable<CaptureTree> */
    public function assembleCaptureItems(CapturablePropertyAssemblerEnvelope $propertyEnvelope): iterable;
}
