<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Arrays;
use stdClass;

use function PHPStan\Testing\assertType;

class ArraysFlattenAssertions
{
    public function assertions(): void
    {
        // assert empty array stays empty
        assertType('array{}', Arrays::flatten([]));
        assertType('array{}', Arrays::flatten([[], []]));

        // assert flat literal array is unaffected (values converted to a 0-indexed list)
        assertType("array{1, 2, 3}", Arrays::flatten([1, 2, 3]));
        assertType("array{'a', 1, true}", Arrays::flatten(['foo' => 'a', 'bar' => 1, 'baz' => true]));

        // assert nested literal arrays are recursively flattened
        assertType("array{'a', 'b', 'c', 'd'}", Arrays::flatten(['a', ['b', ['c', 'd']]]));
        assertType("array{1, 'a', true, null}", Arrays::flatten([1, ['a', [true, [null]]]]));

        // assert mixed literal/object leaves
        assertType('array{stdClass, 1}', Arrays::flatten([new stdClass(), [1]]));

        // assert dynamic single-level array
        /** @var array<string, int> $data */
        $data = ['foo' => 1, 'bar' => 2];
        assertType('list<int>', Arrays::flatten($data));

        // assert dynamic nested array
        /** @var array<string, array<int, int>> $data */
        $data = ['foo' => [1, 2], 'bar' => [3]];
        assertType('list<int>', Arrays::flatten($data));

        // assert dynamic array with mixed leaf types
        /** @var array<int|string, string|array<int, bool>> $data */
        $data = ['foo', [true, false]];
        assertType('list<bool|string>', Arrays::flatten($data));

        // assert dynamic single-level list
        /** @var list<int> $data */
        $data = [1, 2, 3];
        assertType('list<int>', Arrays::flatten($data));

        // assert dynamic nested list
        /** @var list<list<int>> $data */
        $data = [[1, 2], [3]];
        assertType('list<int>', Arrays::flatten($data));

        // assert a leaf whose type isn't guaranteed to be array-or-not (e.g. `mixed`) is treated as an opaque leaf
        /** @var array<int, mixed> $data */
        $data = [1, 'a'];
        assertType('list<mixed>', Arrays::flatten($data));
    }

    public function assertUnpackedArgument(): void
    {
        /** @var list<array<int, int>> $data */
        $data = [[1, 2], [3]];

        // assert an unpacked (spread) argument falls back to the raw declared return type, since its value type
        // describes the spread expression itself rather than the single `array $array` parameter
        assertType('list<T (method DR\Utils\Arrays::flatten(), parameter)>', Arrays::flatten(...$data));
    }

    /**
     * @param array{1, 2}|array{3, 4, 5} $data
     */
    public function assertUnionOfLiteralShapes(array $data): void
    {
        // assert union of literal array shapes is flattened per variant, not narrowed to the first one
        assertType('array{1, 2}|array{3, 4, 5}', Arrays::flatten($data));
    }

    /**
     * @param list<int>|array{string} $data
     */
    public function assertUnionOfListAndLiteralShape(array $data): void
    {
        // assert union of a list and a literal array shape is flattened per variant
        assertType('list<int|string>', Arrays::flatten($data));
    }

    /**
     * @param array<string, list<int>|array{string}> $data
     */
    public function assertNestedUnionOfShapes(array $data): void
    {
        // assert a nested union of array shapes (within a value position) is flattened, not narrowed to the first one
        assertType('list<int|string>', Arrays::flatten($data));
    }
}
