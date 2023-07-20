<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

use DR\Utils\EquatableInterface;

class MockEquatable implements EquatableInterface
{
    public function equalsTo(mixed $other): bool
    {
        return $this === $other;
    }
}
