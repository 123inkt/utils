<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

use DR\Utils\ComparableInterface;

class MockComparable implements ComparableInterface
{
    public function __construct(public readonly int $value)
    {
    }

    public function compareTo(mixed $other): int
    {
        assert(is_object($other));
        assert($other instanceof ComparableInterface);

        return $this->value <=> $other->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
