<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\AssertIsListReturnExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AssertIsListReturnExtension::class)]
class AssertIsListReturnExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new AssertIsListReturnExtension();
        self::assertSame(Assert::class, $extension->getClass());
    }
}
