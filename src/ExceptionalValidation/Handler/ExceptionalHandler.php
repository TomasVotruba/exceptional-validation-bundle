<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Handler;

use PhPhD\ExceptionalValidation\Assembler\CaptureTreeAssembler;
use PhPhD\ExceptionalValidation\Formatter\ExceptionViolationsListFormatter;
use PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException;
use Throwable;

final class ExceptionalHandler implements ExceptionHandler
{
    public function __construct(
        private readonly CaptureTreeAssembler $captureTreeAssembler,
        private readonly ExceptionViolationsListFormatter $violationsFormatter,
    ) {
    }

    /**
     * @return never
     *
     * @throws Throwable
     */
    public function capture(object $message, Throwable $exception): void
    {
        $captureTree = $this->captureTreeAssembler->assembleTree($message);

        if (null === $captureTree) {
            throw $exception;
        }

        $caughtException = $captureTree->capture($exception);

        if (null === $caughtException) {
            throw $exception;
        }

        $violationList = $this->violationsFormatter->formatViolations([$caughtException]);

        throw new ExceptionalValidationFailedException($message, $violationList, $exception);
    }
}
