<?php

declare(strict_types=1);

namespace DR\Utils\Tests\PHPStan\Extension;

use DR\Utils\Arrays;
use DR\Utils\PHPStan\Extension\ArraysRemoveTypesReturnExtension;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArraysRemoveTypesReturnExtension::class)]
class ArraysRemoveTypesReturnExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new ArraysRemoveTypesReturnExtension($this->createMock(TypeNarrower::class));
        self::assertSame(Arrays::class, $extension->getClass());
    }
}
