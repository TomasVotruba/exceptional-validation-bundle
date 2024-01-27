<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Formatter;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ExceptionViolationsListFormatter
{
    /** @param non-empty-list<CaughtException> $caughtExceptions */
    public function formatViolations(array $caughtExceptions): ConstraintViolationListInterface;
}
