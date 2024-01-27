<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Formatter;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ExceptionalViolationsListFormatter implements ExceptionViolationsListFormatter
{
    public function __construct(
        private ExceptionViolationFormatter $violationFormatter,
    ) {
    }

    /** @param non-empty-list<CaughtException> $caughtExceptions */
    public function formatViolations(array $caughtExceptions): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();

        foreach ($caughtExceptions as $caughtException) {
            $violation = $this->violationFormatter->formatViolation($caughtException);

            $violations->add($violation);
        }

        return $violations;
    }
}
