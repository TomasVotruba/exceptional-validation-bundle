<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Tests;

use ArrayIterator;
use LogicException;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CapturesListViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CompositeCaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\NestedCapturableObjectViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertiesViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Formatter\ExceptionalViolationFormatter;
use PhPhD\ExceptionalValidation\Formatter\ExceptionalViolationsListFormatter;
use PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException;
use PhPhD\ExceptionalValidation\Handler\ExceptionalHandler;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\NestedPropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\ObjectPropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\PropertyCapturableException;
use PhPhD\ExceptionalValidation\Tests\Stub\Exception\StaticPropertyCapturedException;
use PhPhD\ExceptionalValidation\Tests\Stub\HandleableMessageStub;
use PhPhD\ExceptionalValidation\Tests\Stub\NestedHandleableMessage;
use PhPhD\ExceptionalValidation\Tests\Stub\NotHandleableMessageStub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \PhPhD\ExceptionalValidation
 * @covers \PhPhD\ExceptionalValidation\Handler\ExceptionalHandler
 * @covers \PhPhD\ExceptionalValidation\Formatter\ExceptionalViolationsListFormatter
 *
 * @internal
 */
final class ExceptionalValidationTest extends TestCase
{
    private ExceptionalHandler $exceptionHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
            ->willReturnMap([
                ['oops', [], 'domain', null, 'oops - translated'],
                ['object.oops', [], 'domain', null, 'object.oops - translated'],
                ['nested.message', [], 'domain', null, 'nested.message - translated'],
            ])
        ;

        $captureListAssemblers = new ArrayIterator();
        $captureTreeAssembler = new CapturableObjectViaReflectionAssembler(
            new CapturablePropertiesViaReflectionAssembler(
                new CompositeCaptureListAssembler($captureListAssemblers),
            ),
        );
        $captureListAssemblers->append(new CapturesListViaReflectionAssembler());
        $captureListAssemblers->append(new NestedCapturableObjectViaReflectionAssembler($captureTreeAssembler));

        $formatter = new ExceptionalViolationFormatter($translator, 'domain');
        $listFormatter = new ExceptionalViolationsListFormatter($formatter);
        $this->exceptionHandler = new ExceptionalHandler($captureTreeAssembler, $listFormatter);
    }

    public function testDoesNotCaptureExceptionForMessageNotHavingExceptionalValidationAttribute(): void
    {
        $message = new NotHandleableMessageStub(123);

        $this->expectExceptionObject($exception = new PropertyCapturableException());

        $this->exceptionHandler->capture($message, $exception);
    }

    public function testCapturesExceptionMappedToProperty(): void
    {
        $message = HandleableMessageStub::createEmpty();
        $rootException = new PropertyCapturableException();

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->exceptionHandler->capture($message, $rootException);
        } catch (ExceptionalValidationFailedException $e) {
            self::assertSame(
                'Message of type "PhPhD\ExceptionalValidation\Tests\Stub\HandleableMessageStub" has failed exceptional validation.',
                $e->getMessage(),
            );
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

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->exceptionHandler->capture($message, new LogicException());
        } catch (ExceptionalValidationFailedException $e) {
            /** @var ConstraintViolationInterface $violation */
            [$violation] = $e->getViolations();

            self::assertSame('invalid text value', $violation->getInvalidValue());

            throw $e;
        }
    }

    public function testCollectsObjectInvalidValue(): void
    {
        $message = HandleableMessageStub::createWithObjectProperty($object = new stdClass());

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->exceptionHandler->capture($message, new ObjectPropertyCapturableException());
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

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->exceptionHandler->capture($message, new StaticPropertyCapturedException());
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

        $this->expectExceptionObject($exception = new NestedPropertyCapturableException());

        $this->exceptionHandler->capture($message, $exception);
    }

    public function testDoesNotCaptureNotInitializedValidNestedObjectProperty(): void
    {
        $message = HandleableMessageStub::createEmpty();

        $this->expectExceptionObject($exception = new NestedPropertyCapturableException());

        $this->exceptionHandler->capture($message, $exception);
    }

    public function testCapturesNestedObjectPropertyException(): void
    {
        $message = HandleableMessageStub::createWithNestedObject(new NestedHandleableMessage());

        $rootException = new NestedPropertyCapturableException();

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->exceptionHandler->capture($message, $rootException);
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
}
