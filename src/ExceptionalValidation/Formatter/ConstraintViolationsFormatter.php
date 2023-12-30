<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Formatter;

use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidation\Model\Tree\CaptureItem;
use PhPhD\ExceptionalValidation\ViolationsFormatter;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConstraintViolationsFormatter implements ViolationsFormatter
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationDomain,
    ) {
    }

    public function formatViolations(CaughtException $caughtException): ConstraintViolationListInterface
    {
        $capturedItem = $caughtException->getCapturedItem();

        $violations = new ConstraintViolationList();

        $this->addViolation($violations, $capturedItem);

        return $violations;
    }

    private function addViolation(ConstraintViolationList $violations, CaptureItem $capturedItem): void
    {
        $message = $capturedItem->getMessage();
        $root = $capturedItem->getRoot();
        $propertyPath = $capturedItem->getPropertyPath();
        $value = $capturedItem->getValue();

        $translatedMessage = $this->translator->trans($message, domain: $this->translationDomain);

        $violations->add(
            new ConstraintViolation(
                $translatedMessage,
                $message,
                [],
                $root,
                $propertyPath->toString(),
                $value,
            ),
        );
    }
}
