<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Tests\Messenger;

use ArrayIterator;
use LogicException;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CompositeCaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\Items\CapturesViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\Nested\NestedObjectViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectViaReflectionTreeAssembler;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertiesViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Capturer\CaptureTreeExceptionCapturer;
use PhPhD\ExceptionalValidation\Formatter\ConstraintViolationsFormatter;
use PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException;
use PhPhD\ExceptionalValidation\Handler\ExceptionHandler;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\NestedPropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\ObjectPropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\PropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\StaticPropertyCapturedException;
use PhPhD\ExceptionalValidation\Tests\Stub\HandleableMessageStub;
use PhPhD\ExceptionalValidation\Tests\Stub\NestedHandleableMessage;
use PhPhD\ExceptionalValidation\Tests\Stub\NotHandleableMessageStub;
use PhPhD\ExceptionalValidationBundle\Messenger\ExceptionalValidationMiddleware;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * @covers \PhPhD\ExceptionalValidationBundle\Messenger\ExceptionalValidationMiddleware
 */
final class ExceptionalValidationMiddlewareTest extends TestCase
{
    private ExceptionalValidationMiddleware $middleware;
    private $nextMiddleware;
    private StackMiddleware $stack;

    protected function setUp(): void
    {
        parent::setUp();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
            ->willReturnMap([
                ['oops', [], 'domain', null, 'oops - translated'],
                ['object.oops', [], 'domain', null, 'object.oops - translated'],
                ['nested.message', [], 'domain', null, 'nested.message - translated'],
            ]);

        $captureListAssemblers = new ArrayIterator();
        $captureTreeAssembler = new CapturableObjectViaReflectionTreeAssembler(
            new CapturablePropertiesViaReflectionAssembler(
                new CompositeCaptureListAssembler($captureListAssemblers),
            ),
        );
        $captureListAssemblers->append(new CapturesViaReflectionAssembler());
        $captureListAssemblers->append(new NestedObjectViaReflectionAssembler($captureTreeAssembler));

        $catcher = new CaptureTreeExceptionCapturer($captureTreeAssembler);
        $formatter = new ConstraintViolationsFormatter($translator, 'domain');
        $exceptionHandler = new ExceptionHandler($catcher, $formatter);

        $this->middleware = new ExceptionalValidationMiddleware($exceptionHandler);
        $this->nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $this->stack = new StackMiddleware([$this->middleware, $this->nextMiddleware]);
    }

    public function testHandlesNotCausingExceptionsMessageThroughStack(): void
    {
        $envelope = Envelope::wrap(HandleableMessageStub::createEmpty());
        $resultEnvelope = Envelope::wrap(new stdClass());

        $this->nextMiddleware
            ->method('handle')
            ->willReturnMap([[$envelope, $this->stack, $resultEnvelope]]);

        $result = $this->middleware->handle($envelope, $this->stack);

        self::assertSame($resultEnvelope, $result);
    }

    public function testRethrowsHandlerFailedExceptionWhenNotCaught(): void
    {
        $envelope = Envelope::wrap(HandleableMessageStub::createEmpty());

        $this->willThrow($exception = new HandlerFailedException($envelope, [new PropertyCapturableException()]));

        $this->expectExceptionObject($exception);

        $this->middleware->handle($envelope, $this->stack);
    }

    public function testDoesNotCaptureExceptionForMessageNotHavingExceptionalValidationAttribute(): void
    {
        $envelope = Envelope::wrap(new NotHandleableMessageStub(123));

        $this->willThrow($thrownException = new PropertyCapturableException());

        $this->expectExceptionObject($thrownException);

        $this->middleware->handle($envelope, $this->stack);
    }

    public function testCapturesExceptionMappedToProperty(): void
    {
        $message = HandleableMessageStub::createEmpty();
        $envelope = Envelope::wrap($message);

        $this->willThrow($rootException = new PropertyCapturableException());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (\PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException $e) {
            self::assertSame('Message of type "PhPhD\ExceptionalValidation\Tests\Stub\HandleableMessageStub" has failed exceptional validation.',
                $e->getMessage());
            self::assertSame($rootException, $e->getPrevious());
            self::assertSame($message, $e->getViolatingMessage());

            $violationList = $e->getViolations();
            self::assertCount(1, $violationList);

            /** @var ConstraintViolationInterface $violation */
            $violation = $violationList[0];
            self::assertSame('property', $violation->getPropertyPath());
            self::assertSame('oops - translated', $violation->getMessage());
            self::assertSame('oops', $violation->getMessageTemplate());
            self::assertSame($message, $violation->getRoot());
            self::assertSame([], $violation->getParameters());
            self::assertNull($violation->getInvalidValue());

            throw $e;
        }
    }

    public function testCollectsInitializedPropertyValue(): void
    {
        $message = HandleableMessageStub::createWithMessageText('invalid text value');
        $envelope = Envelope::wrap($message);

        $this->willThrow(new LogicException());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (ExceptionalValidationFailedException $e) {
            /** @var ConstraintViolationInterface $violation */
            [$violation] = $e->getViolations();

            self::assertSame('invalid text value', $violation->getInvalidValue());

            throw $e;
        }
    }

    public function testCollectsObjectInvalidValue(): void
    {
        $object = new stdClass();
        $message = HandleableMessageStub::createWithObjectProperty($object);
        $envelope = Envelope::wrap($message);

        $this->willThrow(new ObjectPropertyCapturableException());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (ExceptionalValidationFailedException $e) {
            /** @var ConstraintViolationInterface $violation */
            [$violation] = $e->getViolations();

            self::assertSame('object.oops - translated', $violation->getMessage());
            self::assertSame('object.oops', $violation->getMessageTemplate());
            self::assertSame($object, $violation->getInvalidValue());

            throw $e;
        }
    }

    public function testCapturesExceptionsMappedToStaticProperties(): void
    {
        $message = HandleableMessageStub::createEmpty();
        $envelope = Envelope::wrap($message);

        $this->willThrow(new StaticPropertyCapturedException());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (ExceptionalValidationFailedException $e) {
            /** @var ConstraintViolationInterface $violation */
            [$violation] = $e->getViolations();

            self::assertSame('staticProperty', $violation->getPropertyPath());
            self::assertSame('foo', $violation->getInvalidValue());

            throw $e;
        }
    }

    public function testDoesNotCaptureNestedObjectWithoutValidPropertyAttribute(): void
    {
        $message = HandleableMessageStub::createWithOrdinaryObject(new NestedHandleableMessage());
        $envelope = Envelope::wrap($message);

        $this->willThrow($exception = new NestedPropertyCapturableException());

        $this->expectExceptionObject($exception);

        $this->middleware->handle($envelope, $this->stack);
    }

    public function testDoesNotCaptureNotInitializedValidNestedObjectProperty(): void
    {
        $message = HandleableMessageStub::createEmpty();
        $envelope = Envelope::wrap($message);

        $this->willThrow($exception = new NestedPropertyCapturableException());

        $this->expectExceptionObject($exception);

        $this->middleware->handle($envelope, $this->stack);
    }

    public function testCapturesNestedObjectPropertyException(): void
    {
        $nestedObject = new NestedHandleableMessage();
        $message = HandleableMessageStub::createWithNestedObject($nestedObject);
        $envelope = Envelope::wrap($message);

        $this->willThrow($rootException = new NestedPropertyCapturableException());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (ExceptionalValidationFailedException $e) {
            self::assertSame($rootException, $e->getPrevious());

            $violations = $e->getViolations();
            self::assertCount(1, $violations);

            /** @var ConstraintViolationInterface $violation */
            $violation = $violations[0];
            self::assertSame('nested.message - translated', $violation->getMessage());
            self::assertSame('nested.message', $violation->getMessageTemplate());
            self::assertSame('nestedObject.nestedProperty', $violation->getPropertyPath());
            self::assertNull($violation->getInvalidValue());

            throw $e;
        }
    }

    private function willThrow(Throwable $exception): void
    {
        $this->nextMiddleware
            ->method('handle')
            ->willThrowException($exception);
    }
}
