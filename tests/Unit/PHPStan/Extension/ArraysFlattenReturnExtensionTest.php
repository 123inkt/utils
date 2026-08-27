<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit\PHPStan\Extension;

use DR\Utils\Arrays;
use DR\Utils\PHPStan\Extension\ArraysFlattenReturnExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArraysFlattenReturnExtension::class)]
class ArraysFlattenReturnExtensionTest extends TestCase
{
    public function testGetClass(): void
    {
        $extension = new ArraysFlattenReturnExtension();
        self::assertSame(Arrays::class, $extension->getClass());
    }
}
