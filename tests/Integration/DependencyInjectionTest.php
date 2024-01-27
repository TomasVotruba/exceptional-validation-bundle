<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidationBundle\Tests;

use PhPhD\ExceptionalValidation\Assembler\CaptureList\CaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CapturesListViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\CompositeCaptureListAssembler;
use PhPhD\ExceptionalValidation\Assembler\CaptureList\NestedCapturableObjectViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\Object\CapturableObjectViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Assembler\Property\CapturablePropertiesViaReflectionAssembler;
use PhPhD\ExceptionalValidation\Capturer\ExceptionalCapturer;
use PhPhD\ExceptionalValidation\Formatter\ExceptionViolationsListFormatter;
use PhPhD\ExceptionalValidation\Handler\ExceptionalHandler;
use PhPhD\ExceptionalValidation\Handler\ExceptionHandler;
use PhPhD\ExceptionalValidationBundle\Messenger\ExceptionalValidationMiddleware;
use Symfony\Component\VarExporter\LazyObjectInterface;

/**
 * @covers \PhPhD\ExceptionalValidationBundle\PhdExceptionalValidationBundle
 * @covers \PhPhD\ExceptionalValidationBundle\DependencyInjection\PhdExceptionalValidationExtension
 *
 * @internal
 */
final class DependencyInjectionTest extends TestCase
{
    public function testRegisteredServices(): void
    {
        $container = self::getContainer();

        $middleware = $container->get('phd_exceptional_validation');

        self::assertInstanceOf(ExceptionalValidationMiddleware::class, $middleware);

        $exceptionHandler = $container->get('phd_exceptional_validation.exception_handler');
        self::assertInstanceOf(ExceptionHandler::class, $exceptionHandler);
        self::assertInstanceOf(LazyObjectInterface::class, $exceptionHandler);
        self::assertInstanceOf(ExceptionalHandler::class, $exceptionHandler->initializeLazyObject());

        $captureTreeAssembler = $container->get('phd_exceptional_validation.capture_tree_assembler');
        self::assertInstanceOf(CapturableObjectViaReflectionAssembler::class, $captureTreeAssembler);

        $violationsListFormatter = $container->get('phd_exceptional_validation.violations_list_formatter');
        self::assertInstanceOf(ExceptionViolationsListFormatter::class, $violationsListFormatter);
        self::assertInstanceOf(LazyObjectInterface::class, $violationsListFormatter);

        $capturableObjectAssembler = $container->get('phd_exceptional_validation.capturable_object_assembler');
        self::assertInstanceOf(CapturableObjectViaReflectionAssembler::class, $capturableObjectAssembler);

        $capturablePropertiesAssembler = $container->get('phd_exceptional_validation.capturable_properties_assembler');
        self::assertInstanceOf(CapturablePropertiesViaReflectionAssembler::class, $capturablePropertiesAssembler);

        $captureListAssembler = $container->get('phd_exceptional_validation.capture_list_assembler');
        self::assertInstanceOf(CompositeCaptureListAssembler::class, $captureListAssembler);

        $captureListCapturesAssembler = $container->get('phd_exceptional_validation.capture_list_assembler.captures');
        self::assertInstanceOf(CapturesListViaReflectionAssembler::class, $captureListCapturesAssembler);

        $captureListNestedObjectAssembler = $container->get('phd_exceptional_validation.capture_list_assembler.nested_object');
        self::assertInstanceOf(CaptureListAssembler::class, $captureListNestedObjectAssembler);
        self::assertInstanceOf(LazyObjectInterface::class, $captureListNestedObjectAssembler);
        self::assertInstanceOf(NestedCapturableObjectViaReflectionAssembler::class, $captureListNestedObjectAssembler->initializeLazyObject());
    }
}
