<?php

declare(strict_types=1);

namespace DR\Utils;

use BackedEnum;
use Stringable;

class Stringify
{
    /**
     * Convert any variable to a string representation.
     * - bool       > 'true' | 'false'
     * - int        > '123'
     * - float      > '123.45'
     * - string     > 'abc' | empty-string
     * - Stringable > 'abc' | 'empty-string'
     * - []         > 'empty-array'
     * - *          > get_debug_type($value)
     */
    public static function value(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value)) {
            return (string)$value;
        }
        if (is_float($value)) {
            return (string)round($value, 2);
        }
        if ($value instanceof BackedEnum) {
            return (string)$value->value;
        }
        if (is_string($value)) {
            return strlen($value) === 0 ? 'empty-string' : $value;
        }
        if ($value instanceof Stringable) {
            $stringValue = (string)$value;

            return (strlen($stringValue) === 0 ? 'empty-string' : $stringValue) . ' (' . get_debug_type($value) . ')';
        }
        if (is_array($value)) {
            $size = count($value);
            return match (true) {
                $size === 0           => 'empty-array',
                array_is_list($value) => 'array-list(' . $size . ')',
                default               => 'keyed-array(' . $size . ')',
            };
        }

        return get_debug_type($value);
    }
}
