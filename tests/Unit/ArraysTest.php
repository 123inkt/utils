<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use ArrayIterator;
use DR\Utils\Arrays;
use DR\Utils\Tests\Mock\MockComparable;
use DR\Utils\Tests\Mock\MockEquatable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[CoversClass(Arrays::class)]
class ArraysTest extends TestCase
{
    public function testFirstThrowsExceptionOnEmptyArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to obtain first item from array. More context for when it fails.');
        Arrays::first([], 'More context for when it fails.');
    }

    public function testFirst(): void
    {
        static::assertSame('foo', Arrays::first(['foo', 'bar']));
        static::assertSame('foo', Arrays::first(new ArrayIterator(['foo', 'bar'])));
        static::assertFalse(Arrays::first([false, false]));
    }

    public function testFirstOrNull(): void
    {
        static::assertSame('foo', Arrays::firstOrNull(['foo', 'bar']));
        static::assertSame('foo', Arrays::firstOrNull(new ArrayIterator(['foo', 'bar'])));
        static::assertFalse(Arrays::firstOrNull([false, false]));
        static::assertNull(Arrays::firstOrNull([]));
    }

    public function testLastThrowsExceptionOnEmptyArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to obtain last item from array. More context for when it fails.');
        Arrays::last([], 'More context for when it fails.');
    }

    public function testLast(): void
    {
        static::assertSame('bar', Arrays::last(['foo', 'bar']));
        static::assertSame('bar', Arrays::last(new ArrayIterator(['foo', 'bar'])));
        static::assertFalse(Arrays::last([false, false]));
    }

    public function testLastOrNull(): void
    {
        static::assertSame('bar', Arrays::lastOrNull(['foo', 'bar']));
        static::assertSame('bar', Arrays::lastOrNull(new ArrayIterator(['foo', 'bar'])));
        static::assertFalse(Arrays::lastOrNull([false, false]));
        static::assertNull(Arrays::lastOrNull([]));
    }

    public function testMap(): void
    {
        $callback = static fn(int $value, int $key): int => $value * $key;

        static::assertSame([], Arrays::map([], $callback));
        static::assertSame([2 => 200, 3 => 300], Arrays::map([2 => 100, 3 => 100], $callback));
    }

    public function testMapAssoc(): void
    {
        $callback = static fn(array $value): array => [(string)$value[0], $value[1]];

        static::assertSame([], Arrays::mapAssoc([], $callback));
        static::assertSame(['foo' => 'bar'], Arrays::mapAssoc([['foo', 'bar']], $callback));
        static::assertSame(['foo' => 'bar'], Arrays::mapAssoc(new ArrayIterator([['foo', 'bar']]), $callback));
        static::assertSame([2 => false, 4 => true, 6 => false], Arrays::mapAssoc([1, 2, 3], static fn(int $val) => [$val * 2, $val % 2 === 0]));
    }

    public function testReindex(): void
    {
        $callback = static fn(string $value) => strlen($value);

        static::assertSame([], Arrays::reindex([], $callback));
        static::assertSame([3 => 'foo', 6 => 'foobar'], Arrays::reindex(['foo', 'foobar'], $callback));
        static::assertSame([3 => 'foo', 6 => 'foobar'], Arrays::reindex(new ArrayIterator(['foo', 'foobar']), $callback));
    }

    public function testGroupBy(): void
    {
        $data     = ['aaa' => 1, 'bbb' => 2, 'ccc' => 3, 'ddd' => 4];
        $callback = static fn(int $value): string => $value % 2 === 0 ? 'even' : 'odd';
        $expected = [
            'odd'  => [
                'aaa' => 1,
                'ccc' => 3,
            ],
            'even' => [
                'bbb' => 2,
                'ddd' => 4,
            ],
        ];
        static::assertSame($expected, Arrays::groupBy($data, $callback));
    }

    public function testFind(): void
    {
        $objA  = new stdClass();
        $objB  = new stdClass();
        $array = [$objA, $objB];

        static::assertSame($objA, Arrays::find($array, static fn($item) => $item === $objA));
        static::assertSame($objA, Arrays::find(new ArrayIterator($array), static fn($item) => $item === $objA));
    }

    public function testFindFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to find item in items. More context for when it fails.');
        Arrays::find([], static fn() => true, 'More context for when it fails.');
    }

    public function testFindOrNull(): void
    {
        $objA  = new stdClass();
        $objB  = new stdClass();
        $array = [$objA, $objB];

        static::assertSame($objA, Arrays::findOrNull($array, static fn($item) => $item === $objA));
        static::assertSame($objB, Arrays::findOrNull(new ArrayIterator($array), static fn($item) => $item === $objB));
        static::assertNull(Arrays::findOrNull($array, static fn($item) => false));
    }

    public function testRemove(): void
    {
        $objA   = new stdClass();
        $objB   = new stdClass();
        $eqObjA = new MockEquatable();
        $eqObjB = new MockEquatable();
        $array  = [$objA, $objB];

        static::assertSame([1 => $objB], Arrays::remove($array, $objA));
        static::assertSame([0 => $objA], Arrays::remove($array, $objB));
        static::assertSame($array, Arrays::remove($array, 'foobar'));
        static::assertSame([$eqObjA], Arrays::remove([$eqObjA, $eqObjB], $eqObjB));
        static::assertSame([$eqObjA, $eqObjB], Arrays::remove([$eqObjA, $eqObjB], 'foobar'));
    }

    public function testRenameKey(): void
    {
        static::assertSame(['foo' => 'bar'], Arrays::renameKey(['foo' => 'bar'], 'bar', 'foo'));
        static::assertSame(['bar' => 'bar'], Arrays::renameKey(['foo' => 'bar'], 'foo', 'bar'));

        // preserve order yes/no
        static::assertSame(['foz' => 'baz', 'bar' => 'bar'], Arrays::renameKey(['foo' => 'bar', 'foz' => 'baz'], 'foo', 'bar'));
        static::assertSame(['bar' => 'bar', 'foz' => 'baz'], Arrays::renameKey(['foo' => 'bar', 'foz' => 'baz'], 'foo', 'bar', true));

        // overwrite: yes
        static::assertSame(['foz' => 'bar'], Arrays::renameKey(['foo' => 'bar', 'foz' => 'baz'], 'foo', 'foz', false, true));
    }

    public function testRenameKeyDisallowOverwrite(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The toKey "foz" already exists in the array');
        Arrays::renameKey(['foo' => 'bar', 'foz' => 'baz'], 'foo', 'foz');
    }

    public function testRemoveKey(): void
    {
        static::assertSame(['foo' => 'bar'], Arrays::removeKey(['foo' => 'bar'], 'bar'));
        static::assertSame([], Arrays::removeKey(['foo' => 'bar'], 'foo'));
        static::assertSame(['foo' => 'bar'], Arrays::removeKey(['foo' => 'bar'], 'Foo'));
        static::assertSame([], Arrays::removeKey(['foo' => 'bar'], 'Foo', false));

        static::assertSame(['foo'], Arrays::removeKey(['foo', 'bar'], 1));
        static::assertSame(['foo'], Arrays::removeKey(['foo', 'bar'], 1, false));
    }

    public function testRemoveKeysAssociativeArray(): void
    {
        static::assertSame(['foo' => 'bar'], Arrays::removeKeys(['foo' => 'bar'], ['bar']));
        static::assertSame([], Arrays::removeKeys(['foo' => 'bar'], ['foo']));
        static::assertSame(['foo' => 'bar'], Arrays::removeKeys(['foo' => 'bar'], ['Foo']));
        static::assertSame(['FOO' => 'BAR'], Arrays::removeKeys(['foo' => 'bar', 'FOO' => 'BAR'], ['foo']));
        static::assertSame([], Arrays::removeKeys(['foo' => 'bar', 'FOO' => 'BAR'], ['foo'], false));
        static::assertSame([], Arrays::removeKeys(['foo' => 'bar'], ['Foo'], false));
    }

    public function testRemoveKeysIndexedArray(): void
    {
        static::assertSame(['foo'], Arrays::removeKeys(['foo', 'bar'], [1]));
        static::assertSame(['foo'], Arrays::removeKeys(['foo', 'bar'], ['1'], false));
        static::assertSame([1 => 'bar'], Arrays::removeKeys(['foo', 'bar'], [0], false));
        static::assertSame(['foo'], Arrays::removeKeys(['foo', 'bar'], [1], false));
        static::assertSame([], Arrays::removeKeys(['foo', 'bar'], [0, 1, 2], false));
    }

    public function testContains(): void
    {
        static::assertFalse(Arrays::contains(['foobar'], 'unknown'));
        static::assertTrue(Arrays::contains(['foobar'], 'foobar'));
        static::assertTrue(Arrays::contains(new ArrayIterator(['foobar']), 'foobar'));
    }

    public function testSearch(): void
    {
        $objA   = new stdClass();
        $objB   = new stdClass();
        $eqObjA = new MockEquatable();
        $eqObjB = new MockEquatable();

        static::assertFalse(Arrays::search(['foobar'], 'unknown'));
        static::assertSame(0, Arrays::search(['foobar'], 'foobar'));
        static::assertSame('foo', Arrays::search(['foo' => 'bar'], 'bar'));
        static::assertSame('foo', Arrays::search(new ArrayIterator(['foo' => 'bar']), 'bar'));
        static::assertSame(0, Arrays::search([$objA, $objB], $objA));
        static::assertSame(1, Arrays::search([$objA, $objB], $objB));
        static::assertSame(0, Arrays::search([$eqObjA, $eqObjB], $eqObjA));
        static::assertFalse(Arrays::search([$eqObjA, $eqObjB], $objA));
    }

    public function testUnique(): void
    {
        $objA   = new stdClass();
        $objB   = new stdClass();
        $eqObjA = new MockEquatable();
        $eqObjB = new MockEquatable();

        static::assertSame(['foobar'], Arrays::unique(['foobar', 'foobar']));
        static::assertSame(['foo', 'bar'], Arrays::unique(['foo', 'bar']));
        static::assertSame([1], Arrays::unique([1, 1]));
        static::assertSame([1, '1'], Arrays::unique([1, '1']));
        static::assertSame([$objA, $objB], Arrays::unique([$objA, $objB]));
        static::assertSame([$objA], Arrays::unique([$objA, $objA]));
        static::assertSame([0 => $eqObjA, 2 => $eqObjB], Arrays::unique([$eqObjA, $eqObjA, $eqObjB]));
    }

    public function testDiff(): void
    {
        $objA    = new stdClass();
        $objB    = new stdClass();
        $cmpObjA = new MockComparable();
        $cmpObjB = new MockComparable();
        $cmpObjC = new MockComparable();

        // scalars
        static::assertSame(['foo'], Arrays::diff(['foo'], ['bar']));
        static::assertSame(['foo'], Arrays::diff(['foo', 'bar'], ['bar']));
        static::assertSame([], Arrays::diff(['foo', 'bar'], ['foo', 'bar']));
        static::assertSame([], Arrays::diff(['foo', 'bar'], ['bar', 'foo']));

        // objects
        static::assertSame([$objA], Arrays::diff([$objA], [$objB]));
        static::assertSame([], Arrays::diff([$objA], [$objA]));
        static::assertSame([1 => $objB], Arrays::diff([$objA, $objB], [$objA]));
        static::assertSame([], Arrays::diff([$objA, $objB], [$objA, $objB]));
        static::assertSame([], Arrays::diff([$objA, $objB], [$objB, $objA]));

        // comparable interface
        static::assertSame([], Arrays::diff([$cmpObjA, $cmpObjB], [$cmpObjA, $cmpObjB]));
        static::assertSame([$cmpObjA], array_values(Arrays::diff([$cmpObjA, $cmpObjB], [$cmpObjB])));
        static::assertSame([$cmpObjA], array_values(Arrays::diff([$cmpObjA, $cmpObjB], [$cmpObjB, $cmpObjC])));
        static::assertSame([$cmpObjC], array_values(Arrays::diff([$cmpObjB, $cmpObjC], [$cmpObjA, $cmpObjB])));
    }

    public function testEquals(): void
    {
        $objA    = new stdClass();
        $objB    = new stdClass();
        $cmpObjA = new MockComparable();
        $cmpObjB = new MockComparable();
        $cmpObjC = new MockComparable();

        // scalars
        static::assertFalse(Arrays::equals(['foo'], ['bar']));
        static::assertTrue(Arrays::equals(['foo', 'bar'], ['bar', 'foo']));
        static::assertFalse(Arrays::equals(['foo', 'bar'], ['bar']));
        static::assertFalse(Arrays::equals(['bar'], ['foo', 'bar']));

        // objects
        static::assertFalse(Arrays::equals([$objA], [$objB]));
        static::assertTrue(Arrays::equals([$objA], [$objA]));
        static::assertFalse(Arrays::equals([$objA, $objB], [$objA]));
        static::assertTrue(Arrays::equals([$objA, $objB], [$objA, $objB]));
        static::assertTrue(Arrays::equals([$objA, $objB], [$objB, $objA]));

        // comparable interface
        static::assertTrue(Arrays::equals([$cmpObjA, $cmpObjB], [$cmpObjA, $cmpObjB]));
        static::assertFalse(Arrays::equals([$cmpObjA, $cmpObjB], [$cmpObjB]));
        static::assertFalse(Arrays::equals([$cmpObjA, $cmpObjB], [$cmpObjB, $cmpObjC]));
        static::assertFalse(Arrays::equals([$cmpObjB, $cmpObjC], [$cmpObjA, $cmpObjB]));
    }

    public function testExplode(): void
    {
        static::assertSame([], Arrays::explode(',', null));
        static::assertSame([], Arrays::explode(',', ''));
        static::assertSame(['foo'], Arrays::explode(',', 'foo'));
        static::assertSame(['foo', 'bar'], Arrays::explode(',', 'foo,bar'));
    }

    public function testExplodeFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Arrays::explode('', null);
    }

    public function testWrap(): void
    {
        static::assertSame([null], Arrays::wrap(null));
        static::assertSame([], Arrays::wrap(null, false));
        static::assertSame(['foobar'], Arrays::wrap('foobar'));
        static::assertSame(['foobar'], Arrays::wrap(['foobar']));
    }

    public function testRemoveTypes(): void
    {
        $input = [false, 0, '0', 'false', true, 1, '1', 'true'];
        static::assertSame([0, '0', 'false', 1, '1', 'true'], array_values(Arrays::removeTypes($input, ['bool'])));
        static::assertSame(['null'], Arrays::removeTypes(['null', null], ['null']));
        static::assertSame(['1', 2.00], Arrays::removeTypes(['1', 2.00, 3], ['int']));
        static::assertSame([1, '2.00'], Arrays::removeTypes([1, '2.00', 3.00], ['float']));
        static::assertSame([], Arrays::removeTypes(['UT string'], ['string']));
        static::assertSame([], Arrays::removeTypes([[1, 2, 3]], ['array']));
        static::assertSame([], Arrays::removeTypes([new stdClass()], ['stdClass']));
        static::assertSame([], Arrays::removeTypes([new Arrays()], [Arrays::class]));
    }

    public function testRemoveNull(): void
    {
        static::assertSame(['null'], Arrays::removeNull(['null', null]));
    }

    public function testToJson(): void
    {
        static::assertSame('{"name":"John doe","UT":{"foo":"bar"}}', Arrays::toJson(['name' => 'John doe', 'UT' => ['foo' => 'bar']]));
    }

    public function testToJsonWithUnencodableValue(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to convert to Json');

        Arrays::toJson(['invalid' => fopen('php://memory', 'rb')]);
    }

    public function testFromJson(): void
    {
        $jsonString = '{"key": "value", "number": 123}';
        $expectedArray = ["key" => "value", "number" => 123];

        $result = Arrays::fromJson($jsonString);

        static::assertEquals($expectedArray, $result);
    }

    public function testFromJsonWithEmptyJsonString(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JSON string is empty');

        Arrays::fromJson('');
    }

    public function testFromJsonWithInvalidJsonString(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('JsonException: Syntax error in');

        $incompleteJsonString = '{"key": "value", "number": 123';
        Arrays::fromJson($incompleteJsonString);
    }

    public function testFlatten(): void
    {
        $input = [
            'a',
            ['b', 'c'],
            'd',
            [
                'e',
                ['f', 'g']
            ],
            'h'
        ];

        static::assertSame(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'], Arrays::flatten($input));
    }
}
