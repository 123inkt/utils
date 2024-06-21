<?php

declare(strict_types=1);

namespace DR\Utils\Tests\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\AssertTypeTypeSpecifyingExtension;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AssertTypeTypeSpecifyingExtension::class)]
class AssertTypeTypeSpecifyingExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new AssertTypeTypeSpecifyingExtension($this->createMock(TypeNarrower::class));
        self::assertSame(Assert::class, $extension->getClass());
    }
}
