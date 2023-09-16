<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

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
        $this->expectExceptionMessage('Unable to obtain first item from array');
        Arrays::first([]);
    }

    public function testFirst(): void
    {
        static::assertSame('foo', Arrays::first(['foo', 'bar']));
        static::assertFalse(Arrays::first([false, false]));
    }

    public function testFirstOrNull(): void
    {
        static::assertSame('foo', Arrays::firstOrNull(['foo', 'bar']));
        static::assertFalse(Arrays::firstOrNull([false, false]));
        static::assertNull(Arrays::firstOrNull([]));
    }

    public function testLastThrowsExceptionOnEmptyArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to obtain last item from array');
        Arrays::last([]);
    }

    public function testLast(): void
    {
        static::assertSame('bar', Arrays::last(['foo', 'bar']));
        static::assertFalse(Arrays::last([false, false]));
    }

    public function testLastOrNull(): void
    {
        static::assertSame('bar', Arrays::lastOrNull(['foo', 'bar']));
        static::assertFalse(Arrays::lastOrNull([false, false]));
        static::assertNull(Arrays::lastOrNull([]));
    }

    public function testMapAssoc(): void
    {
        $callback = static fn($value) => [(string)$value[0], $value[1]];

        static::assertSame([], Arrays::mapAssoc([], $callback));
        static::assertSame(['foo' => 'bar'], Arrays::mapAssoc([['foo', 'bar']], $callback));
        static::assertSame([2 => false, 4 => true, 6 => false], Arrays::mapAssoc([1, 2, 3], static fn(int $val) => [$val, $val % 2 === 0]));
    }

    public function testReindex(): void
    {
        $callback = static fn($value) => strlen($value);

        static::assertSame([], Arrays::reindex([], $callback));
        static::assertSame([3 => 'foo', 6 => 'foobar'], Arrays::reindex(['foo', 'foobar'], $callback));
    }

    public function testFind(): void
    {
        $objA  = new stdClass();
        $objB  = new stdClass();
        $array = [$objA, $objB];

        static::assertSame($objA, Arrays::find($array, static fn($item) => $item === $objA));
    }

    public function testFindFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to find item in items');
        Arrays::find([], static fn() => true);
    }

    public function testFindOrNull(): void
    {
        $objA  = new stdClass();
        $objB  = new stdClass();
        $array = [$objA, $objB];

        static::assertSame($objA, Arrays::findOrNull($array, static fn($item) => $item === $objA));
        static::assertSame($objB, Arrays::findOrNull($array, static fn($item) => $item === $objB));
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
}
