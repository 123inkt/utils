<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Closures;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Closures::class)]
class ClosuresTest extends TestCase
{
    public function testUnfold(): void
    {
        $class = new class() {
            public int $counter = 0;

            public function __construct()
            {
                $this->counter++;
            }
        };
        $closure = static fn() => $class;

        $unfolded = Closures::unfold($closure);
        static::assertSame($class, $unfolded);
        static::assertSame(1, $class->counter);
        static::assertSame($unfolded, Closures::unfold($closure));
        static::assertSame(1, $class->counter);
    }
}
