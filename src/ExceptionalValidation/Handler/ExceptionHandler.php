<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Handler;

use PhPhD\ExceptionalValidation\ExceptionCapturer;
use PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException;
use PhPhD\ExceptionalValidation\ViolationsFormatter;
use Throwable;

final class ExceptionHandler
{
    public function __construct(
        private ExceptionCapturer $exceptionCapturer,
        private ViolationsFormatter $violationsFormatter,
    ) {
    }

    /**
     * @throws Throwable
     *
     * @return never-return
     */
    public function capture(object $message, Throwable $exception): void
    {
        $caughtException = $this->exceptionCapturer->capture($message, $exception);

        if (null === $caughtException) {
            throw $exception;
        }

        $violationList = $this->violationsFormatter->formatViolations($caughtException);

        throw new ExceptionalValidationFailedException($message, $violationList, $exception);
    }
}
