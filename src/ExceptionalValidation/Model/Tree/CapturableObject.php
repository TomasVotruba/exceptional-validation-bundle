<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model\Tree;

use PhPhD\ExceptionalValidation\Model\CaptureTree;
use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\PropertyPath;
use Throwable;

final class CapturableObject implements CaptureTree
{
    public function __construct(
        private object $object,
        private ?PropertyCaptureList $parent,
        /** @var iterable<CapturableProperty> */
        private iterable $captureProperties,
    ) {
    }

    public function capture(Throwable $exception): ?CaughtException
    {
        foreach ($this->captureProperties as $property) {
            if ($hit = $property->capture($exception)) {
                return $hit;
            }
        }

        return null;
    }

    public function getPropertyPath(): PropertyPath
    {
        return $this->parent?->getPropertyPath() ?? PropertyPath::empty();
    }

    public function getRoot(): object
    {
        return $this->parent?->getRoot() ?? $this->object;
    }

    public function getObject(): object
    {
        return $this->object;
    }
}
