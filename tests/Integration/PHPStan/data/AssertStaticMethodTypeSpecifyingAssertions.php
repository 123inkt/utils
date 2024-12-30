<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertStaticMethodTypeSpecifyingAssertions
{
    public function assertions(): void
    {
        assertType("string", Assert::notNull($this->getValue()));
    }

    private function getValue(): string
    {
        return 'value';
    }
}
