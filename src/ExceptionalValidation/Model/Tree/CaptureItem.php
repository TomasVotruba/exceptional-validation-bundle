<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

/** @api */
final class CaptureItem implements CaptureTree
{
    /** @param class-string<Throwable> $exceptionClass */
    public function __construct(
        private PropertyCaptureList $parent,
        private string $exceptionClass,
        private string $message,
    ) {
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        if (!$exception instanceof $this->exceptionClass) {
            return null;
        }

        return new CaughtException($exception, $this);
    }

    public function getPropertyPath(): PropertyPath
    {
        return $this->parent->getPropertyPath();
    }

    public function getRoot(): object
    {
        return $this->parent->getRoot();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getValue(): mixed
    {
        return $this->parent->getValue();
    }

    public function getProperty(): CapturableProperty
    {
        return $this->parent->getParent();
    }

    public function getParent(): PropertyCaptureList
    {
        return $this->parent;
    }

    /** @return class-string<Throwable> */
    public function getExceptionClass(): string
    {
        return $this->exceptionClass;
    }
}
