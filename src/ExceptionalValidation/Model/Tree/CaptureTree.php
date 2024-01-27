<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

interface CaptureTree
{
    public function capture(Throwable $exception): ?CaughtException;

    public function getPropertyPath(): PropertyPath;

    public function getRoot(): object;
}
