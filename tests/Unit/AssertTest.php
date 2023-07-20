<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[CoversClass(Assert::class)]
class AssertTest extends TestCase
{
    public function testNotNullFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be not null');
        Assert::notNull(null);
    }

    public function testNotNullSuccess(): void
    {
        $rule = new stdClass();
        static::assertSame($rule, Assert::notNull($rule));
    }

    public function testIsArrayFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an array');
        Assert::isArray('foobar'); // @phpstan-ignore-line
    }

    public function testIsArray(): void
    {
        $rules = [new stdClass()];
        static::assertSame($rules, Assert::isArray($rules));
    }

    public function testIsCallable(): void
    {
        $callable = [$this, 'testIsCallable'];
        static::assertSame($callable, Assert::isCallable($callable));
    }

    public function testIsCallableFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be `callable');
        Assert::isCallable('string');
    }

    public function testIsInt(): void
    {
        static::assertSame(5, Assert::isInt(5));
    }

    public function testIsIntFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an int');
        Assert::isInt('string'); // @phpstan-ignore-line
    }

    public function testIsStringFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a string');
        Assert::isString(123); // @phpstan-ignore-line
    }

    public function testIsString(): void
    {
        static::assertSame('string', Assert::isString('string'));
    }

    public function testNotFalseFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be not false');
        Assert::notFalse(false);
    }

    public function testNotFalseSuccess(): void
    {
        $rule = new stdClass();
        static::assertSame($rule, Assert::notFalse($rule));
    }

    public function testInstanceOfFailure(): void
    {
        $object = new stdClass();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be instance of RuntimeException');
        Assert::instanceOf(RuntimeException::class, $object); // @phpstan-ignore-line
    }

    public function testInstanceOfSuccess(): void
    {
        static::assertSame($this, Assert::instanceOf(self::class, $this));
        static::assertSame($this, Assert::instanceOf(TestCase::class, $this));
    }
}
