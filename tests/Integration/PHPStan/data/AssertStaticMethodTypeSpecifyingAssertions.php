<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertStaticMethodTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = "string";
        Assert::notNull($value);
        assertType("'string'", $value);

        $value = null;
        Assert::notNull($value);
        assertType("*NEVER*", $value);
    }
}
