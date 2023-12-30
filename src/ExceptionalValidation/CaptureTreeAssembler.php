<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation;

use PhPhD\ExceptionalValidation\Model\CaptureTree;

interface CaptureTreeAssembler
{
    public function assembleTree(object $message): ?CaptureTree;
}
