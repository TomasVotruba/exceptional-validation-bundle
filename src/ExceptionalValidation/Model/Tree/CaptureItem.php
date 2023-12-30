<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\CaptureTree;
use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

final class CaptureItem implements CaptureTree
{
    public function __construct(
        private PropertyCaptureList $parent,
        private Capture $capture,
    ) {
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        $exceptionClass = $this->capture->getExceptionClass();

        if (!$exception instanceof $exceptionClass) {
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
        return $this->capture->getMessage();
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
        return $this->capture->getExceptionClass();
    }
}
