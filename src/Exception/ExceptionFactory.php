<?php
declare(strict_types=1);

namespace DR\Utils\Exception;

use RuntimeException;
use Stringable;

/**
 * @internal
 */
class ExceptionFactory
{
    public static function createException(string $expectedType, mixed $value, ?string $message = null): RuntimeException
    {
        if (is_bool($value)) {
            $type = ($value ? 'true' : 'false') . ' (bool)';
        } elseif (is_int($value)) {
            $type = $value . ' (int)';
        } elseif (is_float($value)) {
            $type = round($value, 2) . ' (float)';
        } elseif (is_string($value)) {
            $type = strlen($value) === 0 ? 'empty-string' : $value . ' (string)';
        } elseif ($value instanceof Stringable) {
            $stringValue = (string)$value;
            $type        = (strlen($stringValue) === 0 ? 'empty-string' : $stringValue) . ' (' . get_debug_type($value) . ')';
        } elseif (is_array($value) && count($value) === 0) {
            $type = 'empty-array';
        } else {
            $type = get_debug_type($value);
        }

        return new RuntimeException(
            sprintf('Expecting value to be %s, `%s` was given%s', $expectedType, $type, $message === null ? '' : '. ' . $message)
        );
    }
}
