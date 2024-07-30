<?php
declare(strict_types=1);

namespace DR\Utils;

use InvalidArgumentException;
use JsonException;
use RuntimeException;

use function array_diff_key;
use function array_filter;
use function array_flip;
use function array_map;
use function array_udiff;
use function count;
use function end;
use function explode;
use function get_debug_type;
use function is_array;
use function is_object;
use function iterator_to_array;
use function reset;
use function spl_object_hash;
use function strtolower;

class Arrays
{
    /**
     * Returns the first item of the array or exception otherwise
     * @template T
     *
     * @param iterable<T> $items
     *
     * @return T
     */
    public static function first(iterable $items, ?string $failureMessage = null): mixed
    {
        $items = is_array($items) ? $items : iterator_to_array($items);
        if (count($items) === 0) {
            throw new RuntimeException('Unable to obtain first item from array' . ($failureMessage === null ? '' : '. ' . $failureMessage));
        }

        return reset($items);
    }

    /**
     * Returns the first item of the array or null otherwise
     * @template T
     *
     * @param iterable<T> $items
     *
     * @return T|null
     */
    public static function firstOrNull(iterable $items): mixed
    {
        $items = is_array($items) ? $items : iterator_to_array($items);

        return count($items) === 0 ? null : reset($items);
    }

    /**
     * Returns the last item of the array or exception otherwise
     * @template T
     *
     * @param iterable<T> $items
     *
     * @return T
     */
    public static function last(iterable $items, ?string $failureMessage = null): mixed
    {
        $items = is_array($items) ? $items : iterator_to_array($items);
        if (count($items) === 0) {
            throw new RuntimeException('Unable to obtain last item from array' . ($failureMessage === null ? '' : '. ' . $failureMessage));
        }

        return end($items);
    }

    /**
     * Returns the last item of the array or null otherwise
     * @template T
     *
     * @param iterable<T> $items
     *
     * @return T|null
     */
    public static function lastOrNull(iterable $items): mixed
    {
        $items = is_array($items) ? $items : iterator_to_array($items);

        return count($items) === 0 ? null : end($items);
    }

    /**
     * Returns the first item of the array that the callback returns true for. Exception otherwise
     * @template T
     *
     * @param iterable<T>      $items
     * @param callable(T):bool $callback
     *
     * @return T
     */
    public static function find(iterable $items, callable $callback, ?string $failureMessage = null): mixed
    {
        return self::findOrNull($items, $callback)
            ?? throw new RuntimeException('Unable to find item in items' . ($failureMessage === null ? '' : '. ' . $failureMessage));
    }

    /**
     * Returns the first item of the array that the callback returns true for. Null otherwise
     * @template T
     *
     * @param iterable<T>      $items
     * @param callable(T):bool $callback
     *
     * @return T|null
     */
    public static function findOrNull(iterable $items, callable $callback): mixed
    {
        foreach ($items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Similar to <code>array_map</code> but with support for <code>iterable</code> type, and the callback
     * receives the key as the second argument.
     * @template T
     * @template K of array-key
     * @template R
     *
     * @param iterable<K, T>      $items
     * @param (callable(T, K): R) $callback
     *
     * @return array<K, R>
     */
    public static function map(iterable $items, callable $callback): array
    {
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $callback($item, $key);
        }

        return $result;
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
     * @param iterable<T>                      $items
     * @param (callable(T): array{0: K, 1: V}) $callback
     *
     * @return array<K, V>
     */
    public static function mapAssoc(iterable $items, callable $callback): array
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
     * @param iterable<T>      $items
     * @param (callable(T): K) $callback
     *
     * @return array<K, T>
     */
    public static function reindex(iterable $items, callable $callback): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[$callback($item)] = $item;
        }

        return $result;
    }

    /**
     * Group items by the given return value of the callback.
     * <code>
     *     $data   = [1, 2, 3, 4];
     *     $values = Arrays::groupBy($data, fn($val) => $val % 2);
     *     // output: [0 => [2, 4], 1 => [1, 3]]
     * </code>
     * @template TKey of int|string
     * @template TValue
     * @template K of int|string
     *
     * @param iterable<TKey, TValue>      $items
     * @param (callable(TValue, TKey): K) $callback
     *
     * @return array<K, array<TKey, TValue>>
     */
    public static function groupBy(iterable $items, callable $callback): array
    {
        $result = [];
        foreach ($items as $key => $value) {
            $result[$callback($value, $key)][$key] = $value;
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
     * Filter out elements of specified types from an array.
     * @template T
     * @template K
     *
     * @param array<K, T> $items
     * @param string[]    $disallowedTypes
     *
     * @return array<K, T> The filtered array containing only elements not matching the specified types.
     */
    public static function removeTypes(array $items, array $disallowedTypes): array
    {
        return array_filter($items, static fn($element) => in_array(get_debug_type($element), $disallowedTypes, true) === false);
    }

    /**
     * @template T
     * @template K
     * @param array<K, T|null> $items
     *
     * @return array<K, T>
     */
    public static function removeNull(array $items): array
    {
        return self::removeTypes($items, ['null']);
    }

    /**
     * Strict test if `value` is contained within `items`. Method supports `EquatableInterface`.
     *
     * @param iterable<int|string, mixed|EquatableInterface> $items
     */
    public static function contains(iterable $items, mixed $value): bool
    {
        return self::search($items, $value) !== false;
    }

    /**
     * Find the key of the needle in the given array. This method supports the EquatableInterface to
     * determine if 2 different objects are equal.
     * @template T of array-key
     *
     * @param iterable<T, mixed|EquatableInterface> $items
     *
     * @phpstan-return T|false
     */
    public static function search(iterable $items, mixed $needle): int|string|false
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
     * Tests if the given arrays are equal. This method supports the ComparableInterface to determine if
     * 2 different objects are equal.
     * @template T of mixed|ComparableInterface
     * @template K of mixed|ComparableInterface
     *
     * @param T[] $itemsA
     * @param K[] $itemsB
     */
    public static function equals(array $itemsA, array $itemsB): bool
    {
        return count(self::diff($itemsA, $itemsB)) + count(self::diff($itemsB, $itemsA)) === 0;
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
     * Converts an array to a JSON string.
     */
    public static function toJson(array $items, ?string $failureMessage = null): string
    {
        try {
            return json_encode($items, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e . ' ' . $failureMessage);
        }
    }

    /**
     * Converts a JSON string to an array
     */
    public static function fromJson(string $jsonString, ?string $failureMessage = null): array
    {
        if (strlen($jsonString) === 0) {
            throw new RuntimeException('JSON string is empty' . ($failureMessage === null ? '' : '. ' . $failureMessage));
        }

        try {
            return Assert::isArray(json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            throw new RuntimeException($e . ' ' . $failureMessage);
        }
    }
}
