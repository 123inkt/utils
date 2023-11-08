<?php
declare(strict_types=1);

namespace DR\Utils;

use InvalidArgumentException;
use RuntimeException;

use function array_udiff;
use function count;
use function end;
use function explode;
use function is_object;
use function reset;
use function spl_object_hash;

class Arrays
{
    /**
     * Returns the first item of the array or exception otherwise
     * @template T
     *
     * @param T[] $items
     *
     * @return T
     */
    public static function first(array $items): mixed
    {
        if (count($items) === 0) {
            throw new RuntimeException('Unable to obtain first item from array');
        }

        return reset($items);
    }

    /**
     * Returns the first item of the array or null otherwise
     * @template T
     *
     * @param T[] $items
     *
     * @return T|null
     */
    public static function firstOrNull(array $items): mixed
    {
        return count($items) === 0 ? null : reset($items);
    }

    /**
     * Returns the last item of the array or exception otherwise
     * @template T
     *
     * @param T[] $items
     *
     * @return T
     */
    public static function last(array $items): mixed
    {
        if (count($items) === 0) {
            throw new RuntimeException('Unable to obtain last item from array');
        }

        return end($items);
    }

    /**
     * Returns the last item of the array or null otherwise
     * @template T
     *
     * @param T[] $items
     *
     * @return T|null
     */
    public static function lastOrNull(array $items): mixed
    {
        return count($items) === 0 ? null : end($items);
    }

    /**
     * Returns the first item of the array that the callback returns true for. Exception otherwise
     * @template T
     *
     * @param T[]              $items
     * @param callable(T):bool $callback
     *
     * @return T|null
     */
    public static function find(array $items, callable $callback): mixed
    {
        return self::findOrNull($items, $callback) ?? throw new RuntimeException('Unable to find item in items');
    }

    /**
     * Returns the first item of the array that the callback returns true for. Null otherwise
     * @template T
     *
     * @param T[]              $items
     * @param callable(T):bool $callback
     *
     * @return T|null
     */
    public static function findOrNull(array $items, callable $callback): mixed
    {
        foreach ($items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Map an array to key-value pair by the given return values of the callback.
     * <code>
     *      $data = [1, 2, 3];
     *      $values = Arrays::mapAssoc($data, fn($val) => [$val, $val % 0 === 0]);
     *      // output: [1 => false, 2 => true, 3 => false]
     * </code>
     * @template T
     * @template V
     * @template K of int|string
     *
     * @param T[]                              $items
     * @param (callable(T): array{0: K, 1: V}) $callback
     *
     * @return array<K, V>
     */
    public static function mapAssoc(array $items, callable $callback): array
    {
        $result = [];
        foreach ($items as $item) {
            $keyValuePair             = $callback($item);
            $result[$keyValuePair[0]] = $keyValuePair[1];
        }

        return $result;
    }

    /**
     * Map an array to new key values
     * <code>
     *      $data = [1, 2, 3];
     *      $values = Arrays::reindex($data, fn($val) => $val * 2);
     *      // output: [2 => 1, 4 => 2, 6 => 3]
     * </code>
     * @template T
     * @template K of int|string
     *
     * @param T[]              $items
     * @param (callable(T): K) $callback
     *
     * @return array<K, T>
     */
    public static function reindex(array $items, callable $callback): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[$callback($item)] = $item;
        }

        return $result;
    }

    /**
     * Remove an item from the given array. This method supports the EquatableInterface to
     * determine if 2 different objects are equal.
     * @template T of mixed|EquatableInterface
     *
     * @param T[] $items
     * @param T   $item
     *
     * @return T[]
     */
    public static function remove(array $items, mixed $item): array
    {
        $index = self::search($items, $item);
        if ($index !== false) {
            unset($items[$index]);
        }

        return $items;
    }

    /**
     * Remove an item from the given array by the given key.
     * @template T of array<int|string, mixed>
     *
     * @param T $items
     *
     * @return T
     */
    public static function removeKey(array $items, int|string $key, bool $caseSensitive = true): array
    {
        if ($caseSensitive) {
            unset($items[$key]);

            return $items;
        }

        return self::removeKeys($items, [$key], false);
    }

    /**
     * Remove all items from the given array by the given keys.
     * @template T of array<int|string, mixed>
     *
     * @param T                 $items
     * @param array<int|string> $keys
     *
     * @return T
     */
    public static function removeKeys(array $items, array $keys, bool $caseSensitive = true): array
    {
        if ($caseSensitive) {
            return array_diff_key($items, array_flip($keys));
        }

        // force all keys to be lowercase string
        $keys = array_map(static fn($key) => strtolower((string)$key), $keys);

        return array_filter($items, static fn($itemKey) => in_array(strtolower((string)$itemKey), $keys, true) === false, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Strict test if `value` is contained within `items`. Method supports `EquatableInterface`.
     *
     * @param array<mixed|EquatableInterface> $items
     */
    public static function contains(array $items, mixed $value): bool
    {
        return self::search($items, $value) !== false;
    }

    /**
     * Find the key of the needle in the given array. This method supports the EquatableInterface to
     * determine if 2 different objects are equal.
     * @template T of array-key
     *
     * @param array<T, mixed|EquatableInterface> $items
     *
     * @phpstan-return T|false
     */
    public static function search(array $items, mixed $needle): int|string|false
    {
        foreach ($items as $key => $value) {
            if ($value === $needle || ($needle instanceof EquatableInterface && $needle->equalsTo($value))) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Make the array unique. This method supports the EquatableInterface to
     * determine if 2 different objects are equal.
     * @template T of mixed|EquatableInterface
     *
     * @param T[] $items
     *
     * @return T[]
     */
    public static function unique(array $items): array
    {
        $result = [];
        foreach ($items as $key => $item) {
            if (self::search($result, $item) === false) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * Computes the difference of arrays. Will return the items in $itemsA that are not
     * present in $itemsB. This method supports the ComparableInterface to determine if
     * 2 different objects are equal.
     * @template T of mixed|ComparableInterface
     * @template K of mixed|ComparableInterface
     *
     * @param T[] $itemsA
     * @param K[] $itemsB
     *
     * @return T[]
     */
    public static function diff(array $itemsA, array $itemsB): array
    {
        return array_udiff(
            $itemsA,
            $itemsB,
            static function ($itemA, $itemB): int {
                if ($itemA instanceof ComparableInterface) {
                    return $itemA->compareTo($itemB);
                }

                $keyA = is_object($itemA) ? spl_object_hash($itemA) : (string)$itemA;
                $keyB = is_object($itemB) ? spl_object_hash($itemB) : (string)$itemB;

                return strcmp($keyA, $keyB);
            }
        );
    }

    /**
     * Split a string by given separator, in case `value` is empty string or null, will return empty array.
     * @return ($value is ''|null ? array{} : string[])
     */
    public static function explode(string $separator, ?string $value): array
    {
        if ($separator === '') {
            throw new InvalidArgumentException('Separator cannot be empty');
        }

        if ($value === '' || $value === null) {
            return [];
        }

        return explode($separator, $value);
    }

    /**
     * If the given value is not an array, wrap it in an array.
     * Optionally you can skip wrapping if the value is null, if $wrapNull is false an empty array will be returned.
     * @template T
     *
     * @param T|T[] $value
     *
     * @return ($value is null ? ($wrapNull is true ? array{null} : array{}) : T[])
     */
    public static function wrap(mixed $value, bool $wrapNull = true): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($wrapNull === false && $value === null) {
            return [];
        }

        return [$value];
    }


    /**
     * Filter out elements of specified types from an array.
     * @template T
     *
     * @param T[]      $items
     * @param string[] $disallowedTypes
     *
     * @return T[] The filtered array containing only elements not matching the specified types.
     */
    public static function removeTypes(array $items, array $disallowedTypes): array
    {
        return array_filter($items, fn($element) => in_array(get_debug_type($element), $disallowedTypes, true) === false);
    }

    /**
     * @template T
     * @template K
     * @param array<K, T|null> $items
     *
     * @return array<K, T>
     */
    public static function removeNull($items): array
    {
        /** @var array<K, T> $result */
        $result = self::removeTypes($items, ['null']);

        return $result;
    }
}
