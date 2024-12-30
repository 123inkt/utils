<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Assert;
use DR\Utils\Stringify;
use PHPUnit\Framework\TestCase;

class PHPStanTest extends TestCase
{
    public function testPhpStan(): void
    {
        $value = Stringify::value(true);
        static::assertNotNull($value);
    }

    public function testAssert(): void {
        $value = Stringify::value(true);
        Assert::notNull($value);
    }
}
