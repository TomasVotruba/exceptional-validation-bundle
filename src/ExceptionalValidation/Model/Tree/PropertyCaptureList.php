<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaptureTree;
use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

final class PropertyCaptureList implements CaptureTree
{
    public function __construct(
        private CapturableProperty $parent,
        /** @var iterable<CaptureItem|CapturableObject> $captures */
        private iterable $captures,
    ) {
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        foreach ($this->captures as $capture) {
            if ($hit = $capture->capture($exception)) {
                return $hit;
            }
        }

        return null;
    }

    public function getPropertyPath(): PropertyPath
    {
        return $this->parent->getPropertyPath();
    }

    public function getRoot(): object
    {
        return $this->parent->getRoot();
    }

    public function getParent(): CapturableProperty
    {
        return $this->parent;
    }

    public function getValue(): mixed
    {
        return $this->parent->getValue();
    }
}
