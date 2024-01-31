<?php

declare(strict_types=1);

namespace Rela589n\Rector8435\Tests;

use PHPUnit\Framework\TestCase;
use Rela589n\Rector8435\Foo;

final class FooTest extends TestCase
{
    private Foo $foo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->foo = new Foo(234);
    }

    public function testContainsInt(): void
    {
        self::assertSame(234, $this->foo->getI());
    }
}
