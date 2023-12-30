<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ViolationsFormatter
{
    public function formatViolations(CaughtException $caughtException): ConstraintViolationListInterface;
}
