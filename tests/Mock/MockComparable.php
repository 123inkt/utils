<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

use DR\Utils\ComparableInterface;

class MockComparable implements ComparableInterface
{
    public function compareTo(mixed $other): int
    {
        return $this === $other ? 0 : 1;
    }
}
