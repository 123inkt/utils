<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Arrays;
use DR\Utils\Tests\Mock\MockEquatable;
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
    }

    public function testFirstOrNull(): void
    {
        static::assertSame('foo', Arrays::firstOrNull(['foo', 'bar']));
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
    }

    public function testLastOrNull(): void
    {
        static::assertSame('bar', Arrays::lastOrNull(['foo', 'bar']));
        static::assertNull(Arrays::lastOrNull([]));
    }

    public function testMapAssoc(): void
    {
        $callback = static fn($value) => [(string)$value[0], $value[1]];

        static::assertSame([], Arrays::mapAssoc([], $callback));
        static::assertSame(['foo' => 'bar'], Arrays::mapAssoc([['foo', 'bar']], $callback));
    }

    public function testReindex(): void
    {
        $callback = static fn($value) => strlen($value);

        static::assertSame([], Arrays::reindex([], $callback));
        static::assertSame([3 => 'foo', 6 => 'foobar'], Arrays::reindex(['foo', 'foobar'], $callback));
    }

    public function testTryFind(): void
    {
        $objA  = new stdClass();
        $objB  = new stdClass();
        $array = [$objA, $objB];

        static::assertSame($objA, Arrays::tryFind($array, static fn($item) => $item === $objA));
        static::assertSame($objB, Arrays::tryFind($array, static fn($item) => $item === $objB));
        static::assertNull(Arrays::tryFind($array, static fn($item) => false));
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
        $objA   = new stdClass();
        $objB   = new stdClass();
        $eqObjA = new MockEquatable();
        $eqObjB = new MockEquatable();
        $eqObjC = new MockEquatable();

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

        // equatable interface
        static::assertSame([], Arrays::diff([$eqObjA, $eqObjB], [$eqObjA, $eqObjB]));
        static::assertSame([$eqObjA], array_values(Arrays::diff([$eqObjA, $eqObjB], [$eqObjB])));
        static::assertSame([$eqObjA], array_values(Arrays::diff([$eqObjA, $eqObjB], [$eqObjB, $eqObjC])));
        static::assertSame([$eqObjC], array_values(Arrays::diff([$eqObjB, $eqObjC], [$eqObjA, $eqObjB])));
    }
}
