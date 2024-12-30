<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertFalseTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = false;
        Assert::false($value);
        assertType("false", $value);

        $value = true;
        Assert::false($value);
        assertType("*NEVER*", $value);
    }
}
