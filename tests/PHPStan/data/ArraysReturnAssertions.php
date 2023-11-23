<?php

declare(strict_types=1);

namespace DR\Utils\Tests\PHPStan\data;

use DR\Utils\Arrays;
use stdClass;

use function PHPStan\Testing\assertType;

class ArraysReturnAssertions
{
    public function assertions(): void
    {
        // assert one type is removed
        assertType("array{foo: 124, 0: 'string'}", Arrays::removeTypes(['foo' => 124, 'string', null], ['null']));

        // assert result should be empty array
        assertType("array{}", Arrays::removeTypes([123], ['int']));

        // assert array is unaffected
        assertType("array{123}", Arrays::removeTypes([123], ['string']));
        assertType("array{foo: 'bar'}", Arrays::removeTypes(['foo' => 'bar'], ['int']));

        // assert remove multiple types
        assertType("array{123}", Arrays::removeTypes([123, 'string', true], ['string', 'bool']));

        // assert remove class-string
        assertType("array{123}", Arrays::removeTypes([123, new stdClass()], [stdClass::class]));
        assertType("array{1: stdClass}", Arrays::removeTypes([123, new stdClass()], ['int']));

        // assert dynamic key-value array
        /** @var array<string, int|string> $data */
        $data = ['foo' => 'bar', 'test' => 123];
        assertType("array<string, string>", Arrays::removeTypes($data, ['int']));

        // assert dynamic multi-value array
        /** @var array<int|string|true|null> $data */
        $data = [123, 'string', true, null];
        assertType("array<string|true|null>", Arrays::removeTypes($data, ['int']));
    }
}
