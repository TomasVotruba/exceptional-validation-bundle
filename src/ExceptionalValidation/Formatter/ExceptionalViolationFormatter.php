<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Formatter;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExceptionalViolationFormatter implements ExceptionViolationFormatter
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationDomain,
    ) {
    }

    public function formatViolation(CaughtException $caughtException): ConstraintViolationInterface
    {
        $capturedItem = $caughtException->getCapturedItem();

        $message = $capturedItem->getMessage();
        $root = $capturedItem->getRoot();
        $propertyPath = $capturedItem->getPropertyPath();
        $value = $capturedItem->getValue();

        $translatedMessage = $this->translator->trans($message, domain: $this->translationDomain);

        return new ConstraintViolation(
            $translatedMessage,
            $message,
            [],
            $root,
            $propertyPath->toString(),
            $value,
        );
    }
}
