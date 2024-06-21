<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\AssertTypeReturnExtension;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AssertTypeReturnExtension::class)]
class AssertTypeReturnExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new AssertTypeReturnExtension($this->createMock(TypeNarrower::class));
        self::assertSame(Assert::class, $extension->getClass());
    }
}
