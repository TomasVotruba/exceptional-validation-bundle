<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation;

use Attribute;
use Exception;
use Throwable;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Capture
{
    /** @api */
    public function __construct(
        /** @var class-string<Exception> */
        private string $exception,
        private string $message,
    ) {
    }

    /** @return class-string<Throwable> */
    public function getExceptionClass(): string
    {
        return $this->exception;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
