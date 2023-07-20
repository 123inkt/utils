<?php

declare(strict_types=1);

namespace DR\Utils;

interface ComparableInterface
{
    /**
     * @phpstan-return -1|0|1
     */
    public function compareTo(mixed $other): int;
}
