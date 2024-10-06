<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit\PHPStan\Lib;

use DR\Utils\PHPStan\Lib\TypeNarrower;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TypeNarrower::class)]
class TypeNarrowerTest extends TestCase
{
    public function testGetTypesFromStringArray(): void
    {
        $arg = new Arg($this->createMock(Expr::class));

        $narrower = $this->getMockBuilder(TypeNarrower::class)
            ->disableOriginalConstructor()
            ->getMock();

        static::assertSame([], $narrower->getTypesFromStringArray($arg));
    }
}
