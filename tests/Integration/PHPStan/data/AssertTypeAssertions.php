<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertTypeAssertions
{
    public function assertions(): void
    {
        // assert one type is removed
        assertType("int", Assert::type($this->getType(), ['int']));
        assertType("int|null", Assert::type($this->getType(), ['int', 'null']));
        assertType("float", Assert::type($this->getType(), ['float']));
        assertType("array|bool|float|int|string|null", Assert::type($this->getType(), ['bool', 'int', 'float', 'string', 'array', 'object', 'null']));
        assertType("*NEVER*", Assert::type($this->getType(), ['object']));

        // assert argument
        $value = $this->getType();
        Assert::type($value, ['float']);
        assertType("float", $value);

        // assert to invalid type
        $value = $this->getType();
        Assert::type($value, ['object']);
        assertType("*NEVER*", $value);
    }

    private function getType(): bool|int|float|string|array|null
    {
        return true;
    }
}
