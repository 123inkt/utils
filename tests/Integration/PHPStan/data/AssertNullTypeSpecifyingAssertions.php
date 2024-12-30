<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertNullTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        $value = null;
        Assert::null($value);
        assertTynpe("null", $value);

        $value = 'string';
        Assert::null($value);
        assertType("*NEVER*", $value);
    }
}
