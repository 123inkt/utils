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
        $counter = 0;
        $closure = static function () use (&$counter): stdClass {
            $counter++;
            return new stdClass();
        };
        $unfolded1 = Closures::unfold($closure);
        $unfolded2 = Closures::unfold($closure);
        static::assertSame($unfolded1, $unfolded2);
        static::assertSame(1, $counter);
    }
}
