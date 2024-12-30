<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertStringTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = "string";
        Assert::string($value);
        assertType("'string'", $value);

        $value = null;
        Assert::string($value);
        assertType("*NEVER*", $value);
    }
}
