<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

/** @api */
final class PropertyCaptureList implements CaptureTree
{
    public function __construct(
        private readonly CapturableProperty $parent,
        /** @var iterable<CaptureTree> $captures */
        private readonly iterable $captures,
    ) {
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        foreach ($this->captures as $capture) {
            if (null !== ($hit = $capture->capture($exception))) {
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
