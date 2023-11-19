<?php
declare(strict_types=1);

namespace DR\Utils;

use DR\Utils\Exception\ExceptionFactory;

use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_object;
use function is_resource;
use function is_scalar;
use function is_string;

class Assert
{
    /**
     * Assert value is null
     * @template       T
     * @phpstan-assert null $value
     *
     * @param T|null        $value
     *
     * @return null
     */
    public static function null(mixed $value): mixed
    {
        if ($value !== null) {
            throw ExceptionFactory::createException('null', $value);
        }

        return null;
    }

    /**
     * Assert value is not null
     * @template       T
     * @phpstan-assert !null $value
     *
     * @param T|null         $value
     *
     * @return T
     */
    public static function notNull(mixed $value): mixed
    {
        if ($value === null) {
            throw ExceptionFactory::createException('not null', $value);
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
            throw ExceptionFactory::createException('an array', $value);
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
            throw ExceptionFactory::createException('a callable', $value);
        }

        return $value;
    }

    /**
     * Assert value is a resource
     * @template       T
     * @phpstan-assert resource $value
     *
     * @param T                 $value
     *
     * @return T&resource
     */
    public static function resource(mixed $value): mixed
    {
        if (is_resource($value) === false) {
            throw ExceptionFactory::createException('a resource', $value);
        }

        return $value;
    }

    /**
     * Assert value is a scalar
     * @template       T
     * @phpstan-assert scalar $value
     *
     * @param T               $value
     *
     * @return T&scalar
     */
    public static function scalar(mixed $value): mixed
    {
        if (is_scalar($value) === false) {
            throw ExceptionFactory::createException('a scalar', $value);
        }

        return $value;
    }

    /**
     * Assert value is an object
     * @template       T
     * @phpstan-assert object $value
     *
     * @param T               $value
     *
     * @return T&object
     */
    public static function object(mixed $value): mixed
    {
        if (is_object($value) === false) {
            throw ExceptionFactory::createException('an object', $value);
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
            throw ExceptionFactory::createException('an int', $value);
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
            throw ExceptionFactory::createException('a float', $value);
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
            throw ExceptionFactory::createException('a string', $value);
        }

        return $value;
    }

    /**
     * Assert value is a nonempty string
     * @template       T
     * @phpstan-assert non-empty-string $value
     *
     * @param T                         $value
     *
     * @return T&non-empty-string
     */
    public static function nonEmptyString(mixed $value): string
    {
        Assert::string($value);

        if (strlen($value) === 0) {
            throw ExceptionFactory::createException('a non empty string', $value);
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
            throw ExceptionFactory::createException('a boolean', $value);
        }

        return $value;
    }

    /**
     * Assert value is true
     * @template T
     * @phpstan-assert true $value
     *
     * @param T|true        $value
     *
     * @return true
     */
    public static function true(mixed $value): bool
    {
        if ($value !== true) {
            throw ExceptionFactory::createException('true', $value);
        }

        return true;
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
            throw ExceptionFactory::createException('false', $value);
        }

        return false;
    }

    /**
     * Assert value is not false
     * @template       T
     * @phpstan-assert !false $value
     *
     * @param T|false         $value
     *
     * @return T
     */
    public static function notFalse(mixed $value): mixed
    {
        if ($value === false) {
            throw ExceptionFactory::createException('not false', $value);
        }

        return $value;
    }

    /**
     * Assert value is object and of type class-string
     * @template V of mixed
     * @template T of object
     * @phpstan-assert T              $value
     * @phpstan-param V|null          $value
     * @phpstan-param class-string<T> $classString
     * @phpstan-return T&V
     */
    public static function isInstanceOf(mixed $value, string $classString): object
    {
        if ($value instanceof $classString === false) {
            throw ExceptionFactory::createException('instance of ' . $classString, $value);
        }

        return $value;
    }

    /**
     * Assert value is contained within the haystack. Strict comparison is used.
     * @template T
     * @template R of array
     * @phpstan-assert T&value-of<R> $value
     *
     * @param T                    $value
     * @param R                    $haystack
     *
     * @return T&value-of<R>
     */
    public static function inArray(mixed $value, array $haystack): mixed
    {
        if (in_array($value, $haystack, true) === false) {
            throw ExceptionFactory::createException('in array $values', $value);
        }

        return $value;
    }
}
