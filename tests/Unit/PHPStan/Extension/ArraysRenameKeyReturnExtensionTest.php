<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit\PHPStan\Extension;

use DR\Utils\Arrays;
use DR\Utils\PHPStan\Extension\ArraysRemoveTypesReturnExtension;
use DR\Utils\PHPStan\Extension\ArraysRenameKeyReturnExtension;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArraysRenameKeyReturnExtension::class)]
class ArraysRenameKeyReturnExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new ArraysRenameKeyReturnExtension();
        self::assertSame(Arrays::class, $extension->getClass());
    }
}
