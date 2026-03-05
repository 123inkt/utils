<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Closures;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Closures::class)]
class ClosuresTest extends TestCase
{
    public function testUnfold(): void
    {
        $class = new stdClass();
        $closure = static fn() => $class;

        $unfolded = Closures::unfold($closure);
        static::assertSame($class, $unfolded);
        static::assertSame($unfolded, Closures::unfold($closure));
    }
}
