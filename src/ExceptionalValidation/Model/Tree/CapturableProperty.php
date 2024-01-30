<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

/** @api */
final class CapturableProperty implements CaptureTree
{
    private readonly PropertyCaptureList $captureList;

    /** @param iterable<CaptureTree> $captures */
    public function __construct(
        private readonly CapturableObject $parent,
        private readonly string $name,
        private readonly mixed $value,
        iterable $captures,
    ) {
        $this->captureList = new PropertyCaptureList($this, $captures);
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        return $this->captureList->capture($exception);
    }

    public function getPropertyPath(): PropertyPath
    {
        return $this->parent->getPropertyPath()->with($this->name);
    }

    public function getRoot(): object
    {
        return $this->parent->getRoot();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getCaptureList(): PropertyCaptureList
    {
        return $this->captureList;
    }

    public function getParent(): CapturableObject
    {
        return $this->parent;
    }
}
