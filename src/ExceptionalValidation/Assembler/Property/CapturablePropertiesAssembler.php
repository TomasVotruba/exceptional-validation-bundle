<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Property;

use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectAssemblerEnvelope;

/** @internal */
interface CapturablePropertiesAssembler
{
    public function assembleProperties(CapturableObjectAssemblerEnvelope $objectEnvelope): iterable;
}
