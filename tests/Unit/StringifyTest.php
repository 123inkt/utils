<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Assert;
use DR\Utils\Stringify;
use DR\Utils\Tests\Helper\TestEnum;
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
        yield 'enum' => [TestEnum::Foo, 'foo'];
        yield 'empty-string' => ['', 'empty-string'];
        yield 'stringable' => [new MockStringable('foo'), 'foo (' . MockStringable::class . ')'];
        yield 'empty-stringable' => [new MockStringable(''), 'empty-string (' . MockStringable::class . ')'];
        yield 'empty-array' => [[], 'empty-array'];
        yield 'array-list' => [[1, 2, 3], 'array-list(3)'];
        yield 'keyed-array' => [['foo' => 'bar'], 'keyed-array(1)'];
        yield 'object' => [new stdClass(), 'stdClass'];
        yield 'null' => [null, 'null'];

        $resource = fopen('php://memory', 'rb');
        yield 'resource' => [$resource, 'resource (stream)'];
    }

    public function test(): void
    {
        $value = $this->getIntOrString();
        Assert::string($value);

        $value = $this->getString();
        Assert::string($value);

        $value = $this->getInt();
        Assert::string($value);

        $value = $this->getStringOrNull();
        Assert::notNull($value);

        $value = $this->getIntOrString();
        Assert::notNull($value);

        $value = $this->getIntOrString();
        Assert::notFalse($value);

        $value = $this->getBool();
        Assert::true($value);
        Assert::false($value);
        Assert::notFalse($value);
    }

    public function getBool(): bool
    {
        return true;
    }

    public function getIntOrString(): int|string
    {
        return random_int(0, 1) === 1 ? 1 : 'foo';
    }

    public function getString(): string
    {
        return 'foo';
    }

    public function getInt(): int
    {
        return 1;
    }

    public function getStringOrNull(): ?string
    {
        return random_int(0, 1) === 1 ? 'foo' : null;
    }
}
