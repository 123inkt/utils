<?php
declare(strict_types=1);

namespace DR\Utils;

interface EquatableInterface
{
    public function equalsTo(mixed $other): bool;
}
