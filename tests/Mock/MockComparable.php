<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

use DR\Utils\ComparableInterface;

class MockComparable implements ComparableInterface
{
    public function compareTo(mixed $other): int
    {
        assert(is_object($other));
        return strcmp(spl_object_hash($this), spl_object_hash($other));
    }
}
