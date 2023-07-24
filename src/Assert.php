<?php
declare(strict_types=1);

namespace DR\Utils;

use RuntimeException;

class Assert
{
    /**
     * Assert value is not null
     * @template       T
     * @phpstan-assert !null $value
     *
     * @param T|null $value
     *
     * @return T
     */
    public static function notNull(mixed $value): mixed
    {
        if ($value === null) {
            throw new RuntimeException('Expecting value to be not null');
        }

        return $value;
    }

    /**
     * Assert value is array
     * @template       T
     * @phpstan-assert array $value
     *
     * @param T              $value
     *
     * @return T&array
     */
    public static function isArray(mixed $value): array
    {
        if (is_array($value) === false) {
            throw new RuntimeException('Expecting value to be an array');
        }

        return $value;
    }

    /**
     * Assert value is callable
     * @template       T
     * @phpstan-assert callable $value
     *
     * @param T                 $value
     *
     * @return T&callable
     */
    public static function isCallable(mixed $value): callable
    {
        if (is_callable($value) === false) {
            throw new RuntimeException('Expecting value to be `callable`');
        }

        return $value;
    }

    /**
     * Assert value is int
     * @template       T
     * @phpstan-assert int $value
     *
     * @param T            $value
     *
     * @return T&int
     */
    public static function integer(mixed $value): int
    {
        if (is_int($value) === false) {
            throw new RuntimeException('Expecting value to be an int');
        }

        return $value;
    }

    /**
     * Assert value is float
     * @template       T
     * @phpstan-assert float $value
     *
     * @param T              $value
     *
     * @return T&float
     */
    public static function float(mixed $value): float
    {
        if (is_float($value) === false) {
            throw new RuntimeException('Expecting value to be a float');
        }

        return $value;
    }

    /**
     * Assert value is a string
     * @template       T
     * @phpstan-assert string $value
     *
     * @param T               $value
     *
     * @return T&string
     */
    public static function string(mixed $value): string
    {
        if (is_string($value) === false) {
            throw new RuntimeException('Expecting value to be a string');
        }

        return $value;
    }

    /**
     * Assert value is boolean
     * @template T
     * @phpstan-assert bool $value
     *
     * @param T             $value
     *
     * @return T&bool
     */
    public static function boolean(mixed $value): bool
    {
        if (is_bool($value) === false) {
            throw new RuntimeException('Expecting value to be a boolean');
        }

        return $value;
    }

    /**
     * Assert value is false
     * @template T
     * @phpstan-assert false $value
     *
     * @param T|false        $value
     *
     * @return false
     */
    public static function false(mixed $value): bool
    {
        if ($value !== false) {
            throw new RuntimeException('Expecting value to be false');
        }

        return false;
    }

    /**
     * Assert value is not false
     * @template       T
     * @phpstan-assert !false $value
     *
     * @param T|false $value
     *
     * @return T
     */
    public static function notFalse(mixed $value): mixed
    {
        if ($value === false) {
            throw new RuntimeException('Expecting value to be not false');
        }

        return $value;
    }

    /**
     * Assert value is object and of type class-string
     * @template T of object
     * @template V of mixed
     * @phpstan-assert T              $value
     * @phpstan-param class-string<T> $classString
     * @phpstan-param V|null          $value
     * @phpstan-return T&V
     */
    public static function isInstanceOf(string $classString, mixed $value): object
    {
        if ($value instanceof $classString === false) {
            throw new RuntimeException('Expecting value to be instance of ' . $classString);
        }

        return $value;
    }
}
