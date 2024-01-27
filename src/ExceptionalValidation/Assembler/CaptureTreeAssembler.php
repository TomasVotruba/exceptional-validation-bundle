<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler;

use PhPhD\ExceptionalValidation\Model\Tree\CaptureTree;

interface CaptureTreeAssembler
{
    public function assembleTree(object $message): ?CaptureTree;
}
