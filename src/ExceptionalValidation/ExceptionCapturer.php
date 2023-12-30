<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use Throwable;

interface ExceptionCapturer
{
    public function capture(object $message, Throwable $exception): ?CaughtException;
}
