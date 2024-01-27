<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Assembler\Property;

use PhPhD\ExceptionalValidation\Model\Tree\CapturableProperty;

/** @internal */
interface CapturablePropertiesAssembler
{
    /**
     * @template T of object
     *
     * @param CapturablePropertiesAssemblerEnvelope<T> $envelope
     *
     * @return iterable<CapturableProperty>
     */
    public function assembleProperties(CapturablePropertiesAssemblerEnvelope $envelope): iterable;
}
