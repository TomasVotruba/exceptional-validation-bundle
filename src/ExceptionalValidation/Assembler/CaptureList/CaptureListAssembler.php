<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\CaptureList;

use PhPhD\ExceptionalValidation\Model\Tree\CaptureTree;

/** @internal */
interface CaptureListAssembler
{
    /** @return iterable<CaptureTree> */
    public function assembleCaptureItems(CaptureListAssemblerEnvelope $envelope): iterable;
}
