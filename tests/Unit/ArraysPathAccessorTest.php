<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Arrays;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arrays::class)]
class ArraysPathAccessorTest extends TestCase
{
    /**
     * @param non-empty-list<array-key> $path
     */
    #[TestWith([["user", "address", "city"], 'NY'])]
    #[TestWith([["user", "name"], 'John'])]
    #[TestWith([["numbers", 1], 2])]
    public function testFetchByPathSuccess(array $path, mixed $expected): void
    {
        $data = [
            'user'    => ['address' => ['city' => 'NY', 'zip' => 10001], 'name' => 'John'],
            'numbers' => [1, 2]
        ];

        static::assertSame($expected, Arrays::fetchByPath($data, $path));
    }

    public function testFetchByPathNotFoundStrictFalse(): void
    {
        static::assertNull(Arrays::fetchByPath(['foo' => ['bar' => 'baz']], ['foo', 'missing'], false));
    }

    public function testFetchByPathNotFoundStrictTrue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Path "foo -> bar" not found in array');
        Arrays::fetchByPath([], ['foo', 'bar']);
    }

    public function testAssignByPath(): void
    {
        $result = Arrays::assignByPath([], ['user', 'address', 'city'], 'NY');
        static::assertSame(['user' => ['address' => ['city' => 'NY']]], $result);

        $result = Arrays::assignByPath($result, ['user', 'address', 'city'], 'LA');
        static::assertSame(['user' => ['address' => ['city' => 'LA']]], $result);
    }

    public function testAssignByPathThrowsExceptionOnNonArraySegment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to create path for "user -> address -> city" as it contains non-array value: string');
        Arrays::assignByPath(['user' => 'John'], ['user', 'address', 'city'], 'NY');
    }
}
