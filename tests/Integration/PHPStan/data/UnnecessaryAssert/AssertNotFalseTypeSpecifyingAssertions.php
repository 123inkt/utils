<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data\UnnecessaryAssert;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertNotFalseTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = true;
        Assert::notFalse($value);
        assertType("true", $value);

        $value = false;
        Assert::notFalse($value);
        assertType("*NEVER*", $value);
    }
}
