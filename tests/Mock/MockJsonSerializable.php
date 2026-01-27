<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Mock;

use JsonSerializable;

class MockJsonSerializable implements JsonSerializable
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
