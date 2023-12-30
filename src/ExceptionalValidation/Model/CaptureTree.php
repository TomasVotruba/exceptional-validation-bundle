<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model;

use Throwable;

interface CaptureTree
{
    public function capture(Throwable $exception): ?CaughtException;

    public function getPropertyPath(): PropertyPath;

    public function getRoot(): object;
}
