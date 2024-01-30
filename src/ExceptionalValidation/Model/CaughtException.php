<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model;

use LogicException;
use PhPhD\ExceptionalValidation\Model\Tree\CaptureItem;
use Throwable;

/** @api */
final class CaughtException
{
    public function __construct(
        private readonly Throwable $exception,
        private readonly CaptureItem $capturedItem,
    ) {
        $exceptionClass = $this->capturedItem->getExceptionClass();

        if (!$this->exception instanceof $exceptionClass) {
            throw new LogicException('Caught exception must match capture attribute exception class');
        }
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getCapturedItem(): CaptureItem
    {
        return $this->capturedItem;
    }
}
