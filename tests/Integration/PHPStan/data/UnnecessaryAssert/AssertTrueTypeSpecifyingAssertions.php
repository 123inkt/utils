<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data\UnnecessaryAssert;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertTrueTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = true;
        Assert::true($value);
        assertType("true", $value);

        $value = false;
        Assert::true($value);
        assertType("*NEVER*", $value);
    }
}
