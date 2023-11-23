<?php

declare(strict_types=1);

namespace DR\Utils\Tests\PHPStan\data;

use DR\Utils\Arrays;

class Foo
{
    public function assertions(): void
    {
        // assert one type is removed
        \PHPStan\Testing\assertType("array{foo: 124, 0: 'string'}", Arrays::removeTypes(['foo' => 124, 'string', null], ['null']));

        // assert result should be empty array
        \PHPStan\Testing\assertType("array{}", Arrays::removeTypes([123], ['int']));

        // assert array is unaffected
        \PHPStan\Testing\assertType("array{123}", Arrays::removeTypes([123], ['string']));
        \PHPStan\Testing\assertType("array{foo: 'bar'}", Arrays::removeTypes(['foo' => 'bar'], ['int']));

        // assert remove multiple types
        \PHPStan\Testing\assertType("array{123}", Arrays::removeTypes([123, 'string', true], ['string', 'bool']));

        // assert dynamic key-value array
        /** @var array<string, int|string> $data */
        $data = ['foo' => 'bar', 'test' => 123];
        \PHPStan\Testing\assertType("array<string, string>", Arrays::removeTypes($data, ['int']));

        // assert dynamic multi-value array
        /** @var array<int|string|true|null> $data */
        $data = [123, 'string', true, null];
        \PHPStan\Testing\assertType("array<string|true|null>", Arrays::removeTypes($data, ['int']));
    }
}
