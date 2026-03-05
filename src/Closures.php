<?php

declare(strict_types=1);

namespace DR\Utils;

use Closure;

class Closures
{
    /**
     * Calls the Closure and assigns its return value to the variable passed in to this function
     *
     * @template T
     * @param T $closure Is overwritten by its return value
     * @phpstan-param T|Closure(): T $closure
     * @param-out T $closure
     *
     * @phpstan-return T
     */
    public static function unfold(mixed &$closure): mixed
    {
        if ($closure instanceof Closure) {
            /** @phpstan-var T $value */
            $value = ($closure)();
            $closure = $value;
        }

        /** @phpstan-var T $closure */
        return $closure;
    }
}
