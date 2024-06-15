<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Stringify;
use DR\Utils\Tests\Mock\MockStringable;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Stringify::class)]
class StringifyTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testVariable(mixed $value, string $expected): void
    {
        static::assertSame($expected, Stringify::value($value));
    }

    /**
     * @return Generator<string, array<int, mixed>>
     */
    public static function dataProvider(): Generator
    {
        yield 'bool' => [true, 'true'];
        yield 'int' => [1, '1'];
        yield 'float' => [1.1234, '1.12'];
        yield 'string' => ['foo', 'foo'];
        yield 'empty-string' => ['', 'empty-string'];
        yield 'stringable' => [new MockStringable('foo'), 'foo ' . MockStringable::class];
        yield 'empty-stringable' => [new MockStringable(''), 'empty-string ' . MockStringable::class];
        yield 'empty-array' => [[], 'empty-array'];
        yield 'array-list' => [[1, 2, 3], 'array-list(3)'];
        yield 'keyed-array' => [['foo' => 'bar'], 'keyed-array(1)'];
        yield 'object' => [new stdClass(), 'stdClass'];
        yield 'null' => [null, 'null'];

        $resource = fopen('php://memory', 'rb');
        yield 'resource' => [$resource, 'resource (stream)'];
    }
}
