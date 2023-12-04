<?php
declare(strict_types=1);

namespace DR\Utils;

use DR\Utils\Exception\ExceptionFactory;
use RuntimeException;
use Stringable;

use function file_exists;
use function is_bool;
use function is_callable;
use function is_dir;
use function is_file;
use function is_float;
use function is_int;
use function is_object;
use function is_readable;
use function is_resource;
use function is_scalar;
use function is_string;
use function is_writable;

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
     * Assert value is a string or implements __toString
     * @template       T
     * @phpstan-assert string|Stringable $value
     *
     * @param T                          $value
     *
     * @return T&(string|Stringable)
     */
    public static function stringable(mixed $value): string|Stringable
    {
        if (is_string($value) === false && $value instanceof Stringable === false) {
            throw ExceptionFactory::createException('a string or Stringable', $value);
        }

        return $value;
    }

    /**
     * Assert string starts with the given prefix
     * @template T of string|Stringable
     *
     * @param T $value
     *
     * @return T
     */
    public static function startsWith(string|Stringable $value, string $prefix, bool $caseSensitive = true): string|Stringable
    {
        if ($caseSensitive && str_starts_with((string)$value, $prefix) === false) {
            throw new RuntimeException(sprintf('Expecting `%s` to start with `%s`. CaseSensitive', $value, $prefix));
        }

        if ($caseSensitive === false && stripos((string)$value, $prefix) !== 0) {
            throw new RuntimeException(sprintf('Expecting `%s` to start with `%s` CaseInsensitive', $value, $prefix));
        }

        return $value;
    }

    /**
     * Assert string does not start with the given prefix
     * @template T of string|Stringable
     *
     * @param T $value
     *
     * @return T
     */
    public static function notStartsWith(string|Stringable $value, string $prefix, bool $caseSensitive = true): string|Stringable
    {
        if ($caseSensitive && str_starts_with((string)$value, $prefix)) {
            throw new RuntimeException(sprintf('Expecting `%s` to not start with `%s`. CaseSensitive', $value, $prefix));
        }

        if ($caseSensitive === false && stripos((string)$value, $prefix) === 0) {
            throw new RuntimeException(sprintf('Expecting `%s` to not start with `%s` CaseInsensitive', $value, $prefix));
        }

        return $value;
    }

    /**
     * Assert string ends with the given suffix
     * @template T of string|Stringable
     *
     * @param T $value
     *
     * @return T
     */
    public static function endsWith(string|Stringable $value, string $suffix, bool $caseSensitive = true): string|Stringable
    {
        $stringValue = (string)$value;
        if ($caseSensitive && str_ends_with($stringValue, $suffix) === false) {
            throw new RuntimeException(sprintf('Expecting `%s` to end with `%s`. CaseSensitive', $stringValue, $suffix));
        }

        if ($caseSensitive === false && strripos($stringValue, $suffix) !== strlen($stringValue) - strlen($suffix)) {
            throw new RuntimeException(sprintf('Expecting `%s` to end with `%s`. CaseSensitive', $stringValue, $suffix));
        }

        return $value;
    }

    /**
     * Assert string does not start with the given suffix
     * @template T of string|Stringable
     *
     * @param T $value
     *
     * @return T
     */
    public static function notEndsWith(string|Stringable $value, string $suffix, bool $caseSensitive = true): string|Stringable
    {
        $stringValue = (string)$value;
        if ($caseSensitive && str_ends_with($stringValue, $suffix)) {
            throw new RuntimeException(sprintf('Expecting `%s` to not end with `%s`. CaseSensitive', $stringValue, $suffix));
        }

        if ($caseSensitive === false && strripos($stringValue, $suffix) === strlen($stringValue) - strlen($suffix)) {
            throw new RuntimeException(sprintf('Expecting `%s` to not end with `%s`. CaseSensitive', $stringValue, $suffix));
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
     * @param T                      $value
     * @param R                      $haystack
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

    /**
     * Assert if $value is an existing file or directory. Use Assert::file() instead if you need to be sure it is a file.
     * @phpstan-assert string|Stringable $value
     */
    public static function fileExists(mixed $value): string|Stringable
    {
        static::stringable($value);
        if (file_exists((string)$value) === false) {
            throw ExceptionFactory::createException('a file or directory that exists', $value);
        }

        return $value;
    }

    /**
     * Assert if $value is an existing file
     * @phpstan-assert string|Stringable $value
     */
    public static function file(mixed $value): string|Stringable
    {
        static::fileExists($value);
        if (is_file((string)$value) === false) {
            throw ExceptionFactory::createException('a file', $value);
        }

        return $value;
    }

    /**
     * Assert if $value is an existing directory
     * @phpstan-assert string|Stringable $value
     */
    public static function directory(mixed $value): string|Stringable
    {
        static::fileExists($value);
        if (is_dir((string)$value) === false) {
            throw ExceptionFactory::createException('a directory', $value);
        }

        return $value;
    }

    /**
     * Assert if $value or directory exists and is readable
     * @phpstan-assert string $value
     */
    public static function readable(mixed $value): string|Stringable
    {
        static::stringable($value);
        if (is_readable((string)$value) === false) {
            throw ExceptionFactory::createException('readable', $value);
        }

        return $value;
    }

    /**
     * Assert if $value file or directory exists and is writable
     * @phpstan-assert string|Stringable $value
     */
    public static function writable(mixed $value): string|Stringable
    {
        static::stringable($value);
        if (is_writable((string)$value) === false) {
            throw ExceptionFactory::createException('writable', $value);
        }

        return $value;
    }
}
