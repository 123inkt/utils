<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit\Exception;

use DR\Utils\Exception\ExceptionFactory;
use DR\Utils\Tests\Mock\MockStringable;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ExceptionFactory::class)]
class ExceptionFactoryTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testCreateException(mixed $value, string $expectedMessage): void
    {
        $exception = ExceptionFactory::createException('type', $value);
        static::assertSame($expectedMessage, $exception->getMessage());
    }

    /**
     * @return Generator<string, array<int, mixed>>
     */
    public static function dataProvider(): Generator
    {
        $fqcn = MockStringable::class;

        yield 'bool' => [true, 'Expecting value to be type, `true (bool)` was given'];
        yield 'int' => [1, 'Expecting value to be type, `1 (int)` was given'];
        yield 'float' => [1.1234, 'Expecting value to be type, `1.12 (float)` was given'];
        yield 'string' => ['foo', 'Expecting value to be type, `foo (string)` was given'];
        yield 'empty-string' => ['', 'Expecting value to be type, `empty-string` was given'];
        yield 'stringable' => [new MockStringable('foo'), 'Expecting value to be type, `foo (' . $fqcn . ')` was given'];
        yield 'empty-stringable' => [new MockStringable(''), 'Expecting value to be type, `empty-string (' . $fqcn . ')` was given'];
        yield 'empty-array' => [[], 'Expecting value to be type, `empty-array` was given'];
        yield 'object' => [new stdClass(), 'Expecting value to be type, `stdClass` was given'];
        yield 'null' => [null, 'Expecting value to be type, `null` was given'];
    }
}
