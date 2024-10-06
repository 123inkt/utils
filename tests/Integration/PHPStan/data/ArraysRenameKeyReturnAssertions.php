<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Arrays;

use function PHPStan\Testing\assertType;

class ArraysRenameKeyReturnAssertions
{
    public function assertions(): void
    {
        assertType("array{bar: 124}", Arrays::renameKey(['foo' => 124], 'foo', 'bar'));
        assertType("array{bar: 124}", Arrays::renameKey(['bar' => 124], 'foo', 'bar'));
        assertType("array{bar: 124, 0: 'baz'}", Arrays::renameKey(['foo' => 124, 'baz'], 'foo', 'bar'));

        // assert dynamic key-value array
        /** @var array<string, int|string> $data */
        $data = ['foo' => 'bar', 'test' => 123];
        assertType("array<string, int|string>", Arrays::renameKey($data, 'foo', 'bar'));
    }
}
