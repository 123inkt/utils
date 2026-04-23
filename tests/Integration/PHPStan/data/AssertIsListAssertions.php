<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan\data;

use DR\Utils\Assert;

use function PHPStan\Testing\assertType;

class AssertIsListAssertions
{
    public function assertions(): void
    {
        // constant array preserves value type
        assertType("list<1|2|3>", Assert::isList([1, 2, 3]));
        assertType("list<'a'|'b'|'c'>", Assert::isList(['a', 'b', 'c']));
        assertType("list<1|'foo'>", Assert::isList([1, 'foo']));

        // typed array variable preserves value type, drops key type
        /** @var array<string, int> $assocInt */
        $assocInt = [];
        assertType("list<int>", Assert::isList($assocInt));

        /** @var array<int, string> $indexedString */
        $indexedString = [];
        assertType("list<string>", Assert::isList($indexedString));

        /** @var array<int, int|float|string|object|null> $mixedData */
        $mixedData = [];
        assertType("list<float|int|object|string|null>", Assert::isList($mixedData));

        // mixed input falls back to list<mixed>
        /** @var mixed $mixed */
        $mixed = null;
        assertType("list<mixed>", Assert::isList($mixed));
    }
}
