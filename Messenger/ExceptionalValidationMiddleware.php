<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidationBundle\Messenger;

use Exception;
use PhPhD\ExceptionalValidation\Handler\ExceptionHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

/** @readonly */
final class ExceptionalValidationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ExceptionHandler $exceptionHandler,
    ) {
    }

    /** @throws Throwable */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Exception $exception) {
            $this->exceptionHandler->capture($message, $exception);
        }
    }
}
