<?php

declare(strict_types=1);

namespace DR\Utils;

use Closure;

class Closures
{
    /**
     * @template T of object
     * @phpstan-param T|Closure(): T $closure
     * @param-out T $closure
     *
     * @phpstan-return T
     */
    public static function unfold(object &$closure): object
    {
        if ($closure instanceof Closure) {
            /** @phpstan-var T $object */
            $object = ($closure)();
            $closure = $object;
        }

        /** @phpstan-var T $closure */
        return $closure;
    }
}
