<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(Assert::class)]
class AssertIntegerTest extends TestCase
{
    public function testInteger(): void
    {
        static::assertSame(5, Assert::integer(5));
    }

    public function testIntegerFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an int, `string (string)` was given');
        Assert::integer('string');
    }

    public function testPositiveInt(): void
    {
        static::assertSame(5, Assert::positiveInt(5));
    }

    public function testPositiveIntFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a positive int');
        Assert::positiveInt(0);
    }

    public function testNegativeInt(): void
    {
        static::assertSame(-5, Assert::negativeInt(-5));
    }

    public function testNegativeIntFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a negative int');
        Assert::negativeInt(0);
    }

    public function testNonPositiveInt(): void
    {
        static::assertSame(-5, Assert::nonPositiveInt(-5));
        static::assertSame(0, Assert::nonPositiveInt(0));
    }

    public function testNonPositiveIntFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a non-positive int');
        Assert::nonPositiveInt(5);
    }

    public function testNonNegativeInt(): void
    {
        static::assertSame(5, Assert::nonNegativeInt(5));
        static::assertSame(0, Assert::nonNegativeInt(0));
    }

    public function testNonNegativeIntFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a non-negative int');
        Assert::nonNegativeInt(-5);
    }

    #[TestWith([5])]
    #[TestWith(['5'])]
    #[TestWith(['-5'])]
    #[TestWith(['-5.5'])]
    #[TestWith([-5.5])]
    #[TestWith(['-5.5e10'])]
    public function testNumeric(mixed $value): void
    {
        static::assertSame($value, Assert::numeric($value));
    }

    public function testNumericFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be numeric, `foobar (string)` was given');
        Assert::numeric('foobar');
    }
}
