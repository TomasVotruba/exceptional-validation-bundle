<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Capturer;

use PhPhD\ExceptionalValidation\CaptureTreeAssembler;
use PhPhD\ExceptionalValidation\ExceptionCapturer;
use PhPhD\ExceptionalValidation\Model\CaughtException;
use Throwable;

final class CaptureTreeExceptionCapturer implements ExceptionCapturer
{
    public function __construct(
        private CaptureTreeAssembler $captureTreeAssembler,
    ) {
    }

    public function capture(object $message, Throwable $exception): ?CaughtException
    {
        return $this->captureTreeAssembler->assembleTree($message)?->capture($exception);
    }
}
