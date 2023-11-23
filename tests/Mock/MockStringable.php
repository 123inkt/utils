<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

class MockStringable
{
    public function __construct(private readonly string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
