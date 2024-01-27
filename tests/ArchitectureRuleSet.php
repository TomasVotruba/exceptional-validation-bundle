<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Tests;

use PHPat\Selector\ClassNamespace;
use PHPat\Selector\Selector;
use PHPat\Selector\SelectorInterface;
use PHPat\Test\Attributes\TestRule;
use PHPat\Test\Builder\BuildStep;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Assembler\CaptureTreeAssembler;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ExceptionViolationsListFormatter;
use PhPhD\ExceptionalValidation\Handler\ExceptionHandler;
use PhPhD\ExceptionalValidation\Model\CaughtException;
use PhPhD\ExceptionalValidationBundle\Messenger\ExceptionalValidationMiddleware;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 *
 * @api
 */
final class ArchitectureRuleSet
{
    #[TestRule]
    public function testMiddlewareDependencies(): Rule
    {
        return $this->layerRule('middleware');
    }

    #[TestRule]
    public function testExceptionHandlerDependencies(): Rule
    {
        return $this->layerRule('exceptionHandler');
    }

    #[TestRule]
    public function testViolationsFormatterDependencies(): Rule
    {
        return $this->layerRule('violationsFormatter');
    }

    #[TestRule]
    public function testCaptureTreeAssemblerDependencies(): Rule
    {
        return $this->layerRule('captureTreeAssembler');
    }

    #[TestRule]
    public function testCaptureTreeDependencies(): Rule
    {
        return $this->layerRule('model');
    }

    private function layerRule(string $name): BuildStep
    {
        $layer = $this->layers()[$name];

        $layerClasses = $this->$name();

        return PHPat::rule()
            ->classes($layerClasses)
            ->canOnlyDependOn()
            ->classes($layerClasses, ...$layer['deps'])
            ->because($layer['description'] ?? 'It has clearly defined dependency rules in '.self::class.'::layers()');
    }

    /** @return array<string,array{deps:list<SelectorInterface>,description?: string}> */
    private function layers(): array
    {
        return [
            'middleware' => [
                'deps' => [
                    Selector::AND(
                        Selector::isInterface(),
                        $this->exceptionHandler(),
                    ),
                    Selector::inNamespace('Symfony\Component\Messenger')
                ],
            ],
            'exceptionHandler' => [
                'deps' => [
                    Selector::AND(
                        $this->captureTreeAssembler(),
                        Selector::isInterface(),
                    ),
                    Selector::AND(
                        Selector::isInterface(),
                        $this->violationsFormatter(),
                    ),
                    Selector::classname(ConstraintViolationListInterface::class),
                ],
            ],
            'violationsFormatter' => [
                'deps' => [
                    $this->model(),
                    Selector::inNamespace(class_namespace(ConstraintViolationListInterface::class)),
                    Selector::classname(TranslatorInterface::class),
                ],
            ],
            'captureTreeAssembler' => [
                'deps' => [
                    $this->model(),
                    Selector::classname(ExceptionalValidation::class),
                    Selector::classname(Capture::class),
                    Selector::classname(Valid::class),
                ],
            ],
            'model' => [
                'deps' => [],
                'description' => 'Model classes must not depend on anything else',
            ],
        ];
    }

    /** @psalm-suppress UnusedMethod */
    private function middleware(): ClassNamespace
    {
        return Selector::inNamespace(class_namespace(ExceptionalValidationMiddleware::class));
    }

    private function exceptionHandler(): ClassNamespace
    {
        return Selector::inNamespace(class_namespace(ExceptionHandler::class));
    }

    private function violationsFormatter(): ClassNamespace
    {
        return Selector::inNamespace(class_namespace(ExceptionViolationsListFormatter::class));
    }

    private function captureTreeAssembler(): ClassNamespace
    {
        return Selector::inNamespace(class_namespace(CaptureTreeAssembler::class));
    }

    private function model(): ClassNamespace
    {
        return Selector::inNamespace(class_namespace(CaughtException::class));
    }
}

/**
 * @param non-empty-string $class
 *
 * @return non-empty-string
 */
function class_namespace(string $class): string
{
    /** @var non-empty-list<string> $namespaceParts */
    $namespaceParts = array_slice(explode('\\', $class), 0, -1);

    return implode('\\', $namespaceParts);
}
