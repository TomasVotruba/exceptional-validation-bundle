<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Object;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableObject;
use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;

/** @internal */
interface CapturableObjectAssembler
{
    public function assembleTree(object $message, ?CapturableProperty $parent = null): ?CapturableObject;
}
