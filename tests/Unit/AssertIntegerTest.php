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
        Assert::integer('string'); // @phpstan-ignore-line
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
