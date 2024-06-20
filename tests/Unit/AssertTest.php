<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Unit;

use DR\Utils\Assert;
use DR\Utils\Tests\Mock\MockStringable;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Stringable;

#[CoversClass(Assert::class)]
class AssertTest extends TestCase
{
    public function testNullFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be null, `foobar (string)` was given. More context about error.');
        Assert::null('foobar', 'More context about error.');
    }

    public function testNullSuccess(): void
    {
        static::assertNull(Assert::null(null));
    }

    public function testNotNullFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be not null, `null` was given');
        Assert::notNull(null);
    }

    public function testNotNullSuccess(): void
    {
        $object = new stdClass();
        static::assertSame($object, Assert::notNull($object));
    }

    public function testIsArrayFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an array, `foobar (string)` was given');
        Assert::isArray('foobar'); // @phpstan-ignore-line
    }

    public function testIsArray(): void
    {
        $objects = [new stdClass()];
        static::assertSame($objects, Assert::isArray($objects));
    }

    public function testIsCallable(): void
    {
        $callable = [$this, 'testIsCallable'];
        static::assertSame($callable, Assert::isCallable($callable));
    }

    public function testIsCallableFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a callable, `string (string)` was given');
        Assert::isCallable('string');
    }

    public function testScalarSuccess(): void
    {
        static::assertSame(123, Assert::scalar(123));
    }

    public function testScalarFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a scalar, `stdClass` was given');
        Assert::scalar(new stdClass()); // @phpstan-ignore-line
    }

    public function testResourceSuccess(): void
    {
        static::assertSame(STDIN, Assert::resource(STDIN));
    }

    public function testResourceFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a resource, `string (string)` was given');
        Assert::resource('string'); // @phpstan-ignore-line
    }

    public function testObjectSuccess(): void
    {
        $obj = new stdClass();
        static::assertSame($obj, Assert::object($obj));
    }

    public function testObjectFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an object, `string (string)` was given');
        Assert::object('string'); // @phpstan-ignore-line
    }

    public function testInteger(): void
    {
        static::assertSame(5, Assert::integer(5));
    }

    public function testIntegerFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be an int, `string (string)` was given');
        Assert::integer('string'); // @phpstan-ignore-line
    }

    #[TestWith([5])]
    #[TestWith(['5'])]
    #[TestWith(['-5'])]
    #[TestWith(['-5.5'])]
    #[TestWith([-5.5])]
    #[TestWith(['-5.5e10'])]
    public function testNumeric(mixed $value): void
    {
        static::assertSame($value, Assert::numeric($value));
    }

    public function testNumericFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be numeric, `foobar (string)` was given');
        Assert::numeric('foobar');
    }

    public function testFloat(): void
    {
        static::assertSame(5.5, Assert::float(5.5));
    }

    public function testFloatFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a float, `string (string)` was given');
        Assert::float('string'); // @phpstan-ignore-line
    }

    public function testStringFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a string, `123 (int)` was given');
        Assert::string(123); // @phpstan-ignore-line
    }

    public function testString(): void
    {
        static::assertSame('string', Assert::string('string'));
    }

    public function testStringableFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a string or Stringable, `123 (int)` was given');
        Assert::stringable(123); // @phpstan-ignore-line
    }

    public function testStringable(): void
    {
        static::assertSame('string', Assert::stringable('string'));

        $stringable = new MockStringable('string');
        static::assertSame($stringable, Assert::stringable($stringable));
    }

    #[TestWith([Assert::class])]
    #[TestWith(['DR\Utils\Assert'])]
    public function testClassString(string $value): void
    {
        static::assertSame($value, Assert::classString($value));
    }

    public function testClassStringStringFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a string, `123 (int)` was given');
        Assert::classString(123); // @phpstan-ignore-line
    }

    public function testClassStringClassFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a class-string but class does not exist, `DR\Utils\FakeClass (string)` was given');
        Assert::classString('DR\Utils\FakeClass');
    }

    #[TestWith(['string', 'str', true])]
    #[TestWith(['string', 'str', false])]
    #[TestWith(['string', 'STR', false])]
    #[TestWith([new MockStringable('string'), 'str', true])]
    public function testStartsWith(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        static::assertSame($value, Assert::startsWith($value, $suffix, $caseSensitive));
    }

    #[TestWith(['string', 'STR', true])]
    #[TestWith(['string', 'foo', true])]
    #[TestWith(['string', 'foo', false])]
    #[TestWith([new MockStringable('string'), 'STR', true])]
    public function testStartsWithFailure(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches(
            '/^Expecting `string` to start with `\w+`\. (?:CaseSensitive|CaseInsensitive). More context about failure.$/'
        );
        Assert::startsWith($value, $suffix, $caseSensitive, 'More context about failure.');
    }

    #[TestWith(['string', 'STR', true])]
    #[TestWith(['string', 'foo', true])]
    #[TestWith(['string', 'foo', false])]
    #[TestWith([new MockStringable('string'), 'STR', true])]
    public function testNotStartsWith(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        static::assertSame($value, Assert::notStartsWith($value, $suffix, $caseSensitive));
    }

    #[TestWith(['string', 'str', true])]
    #[TestWith(['string', 'STR', false])]
    #[TestWith([new MockStringable('string'), 'str', true])]
    public function testNotStartsWithFailure(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches(
            '/^Expecting `string` to not start with `\w+`\. (?:CaseSensitive|CaseInsensitive). More context about failure.$/'
        );
        Assert::notStartsWith($value, $suffix, $caseSensitive, 'More context about failure.');
    }

    #[TestWith(['string', 'ing', true])]
    #[TestWith(['string', 'ing', false])]
    #[TestWith(['string', 'ING', false])]
    #[TestWith([new MockStringable('string'), 'ing', true])]
    public function testEndsWith(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        static::assertSame($value, Assert::endsWith($value, $suffix, $caseSensitive));
    }

    #[TestWith(['string', 'ING', true])]
    #[TestWith(['string', 'foo', true])]
    #[TestWith(['string', 'foo', false])]
    #[TestWith([new MockStringable('string'), 'ING', true])]
    public function testEndsWithFailure(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches(
            '/^Expecting `string` to end with `\w+`\. (?:CaseSensitive|CaseInsensitive). More context about failure.$/'
        );
        Assert::endsWith($value, $suffix, $caseSensitive, 'More context about failure.');
    }

    #[TestWith(['string', 'ING', true])]
    #[TestWith(['string', 'foo', true])]
    #[TestWith(['string', 'foo', false])]
    #[TestWith([new MockStringable('string'), 'ING', true])]
    public function testNotEndsWith(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        static::assertSame($value, Assert::notEndsWith($value, $suffix, $caseSensitive));
    }

    #[TestWith(['string', 'ing', true])]
    #[TestWith(['string', 'ing', false])]
    #[TestWith(['string', 'ING', false])]
    #[TestWith([new MockStringable('string'), 'ing', true])]
    public function testNotEndsWithFailure(string|Stringable $value, string $suffix, bool $caseSensitive): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches(
            '/^Expecting `string` to not end with `\w+`\. (?:CaseSensitive|CaseInsensitive). More context about failure.$/'
        );
        Assert::notEndsWith($value, $suffix, $caseSensitive, 'More context about failure.');
    }

    public function testNonEmptyArrayFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting array to be non-empty. More context about failure.');
        Assert::nonEmptyArray([], 'More context about failure.'); // @phpstan-ignore-line
    }

    public function testNonEmptyArray(): void
    {
        $objects = [new stdClass()];
        static::assertSame($objects, Assert::nonEmptyArray($objects));
    }

    public function testNonEmptyStringNoStringFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a string, `123 (int)` was given');
        Assert::nonEmptyString(123); // @phpstan-ignore-line
    }

    public function testNonEmptyStringFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a non empty string, `empty-string` was given');
        Assert::nonEmptyString('');
    }

    public function testNonEmptyString(): void
    {
        static::assertSame('string', Assert::nonEmptyString('string'));
    }

    public function testBoolean(): void
    {
        static::assertTrue(Assert::boolean(true));
        static::assertFalse(Assert::boolean(false));
    }

    public function testBooleanFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a boolean, `string (string)` was given');
        Assert::boolean('string'); // @phpstan-ignore-line
    }

    public function testTrueFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be true, `false (bool)` was given');
        Assert::true(false);
    }

    public function testTrueSuccess(): void
    {
        static::assertTrue(Assert::true(true));
    }

    public function testFalseFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be false, `true (bool)` was given');
        Assert::false(true);
    }

    public function testFalseSuccess(): void
    {
        static::assertFalse(Assert::false(false));
    }

    public function testNotFalseFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be not false, `false (bool)` was given');
        Assert::notFalse(false);
    }

    public function testNotFalseSuccess(): void
    {
        $object = new stdClass();
        static::assertSame($object, Assert::notFalse($object));
    }

    public function testIsInstanceOfFailure(): void
    {
        $object = new stdClass();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be instance of RuntimeException, `stdClass` was given');
        Assert::isInstanceOf($object, RuntimeException::class); // @phpstan-ignore-line
    }

    public function testIsInstanceOfSuccess(): void
    {
        static::assertSame($this, Assert::isInstanceOf($this, self::class));
        static::assertSame($this, Assert::isInstanceOf($this, TestCase::class));
    }

    public function testInArrayFailure(): void
    {
        $value    = 5;
        $haystack = [1, '5', false];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be in array $values, `5 (int)` was given');
        Assert::inArray($value, $haystack);
    }

    public function testInArraySuccess(): void
    {
        $values = [5, 'foobar', true, 2.3];

        static::assertSame(5, Assert::inArray(5, $values));
        static::assertSame('foobar', Assert::inArray('foobar', $values));
        static::assertTrue(Assert::inArray(true, $values));
        static::assertSame(2.3, Assert::inArray(2.3, $values));
    }

    #[TestWith(['/directory'])]
    #[TestWith(['/directory/file.txt'])]
    public function testFileExistsSuccess(string $path): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . $path;
        static::assertSame($path, Assert::fileExists($path));
    }

    public function testFileExistsFailure(): void
    {
        $baseDir = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be a file or directory that exists');
        Assert::fileExists($baseDir . '/foobar');
    }

    public function testFileSuccess(): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . '/directory/file.txt';
        static::assertSame($path, Assert::file($path));
    }

    #[TestWith(['/directory', 'Expecting value to be a file'])]
    #[TestWith(['/foobar', 'Expecting value to be a file or directory that exists'])]
    public function testFileFailure(string $path, string $expectedMessage): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . $path;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);
        Assert::file($path);
    }

    public function testDirectorySuccess(): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . '/directory';
        static::assertSame($path, Assert::directory($path));
    }

    #[TestWith(['/directory/file.txt', 'Expecting value to be a directory'])]
    #[TestWith(['/foobar', 'Expecting value to be a file or directory that exists'])]
    public function testDirectoryFailure(string $path, string $expectedMessage): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . $path;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);
        Assert::directory($path);
    }

    #[TestWith(['/directory'])]
    #[TestWith(['/directory/file.txt'])]
    public function testReadableSuccess(string $path): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . $path;
        static::assertSame($path, Assert::readable($path));
    }

    #[TestWith(['/directory'])]
    #[TestWith(['/directory/file.txt'])]
    #[TestWith(['/foobar'])]
    public function testReadableFailure(string $path): void
    {
        $baseDir = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url();
        chmod($baseDir . '/directory/file.txt', 0000);
        chmod($baseDir . '/directory', 0000);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be readable');
        Assert::readable($baseDir . $path);
    }

    #[TestWith(['/directory'])]
    #[TestWith(['/directory/file.txt'])]
    public function testWritableSuccess(string $path): void
    {
        $path = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url() . $path;
        static::assertSame($path, Assert::writable($path));
    }

    #[TestWith(['/directory'])]
    #[TestWith(['/directory/file.txt'])]
    #[TestWith(['/foobar'])]
    public function testWritableFailure(string $path): void
    {
        $baseDir = vfsStream::setup('root', null, ['/directory' => ['file.txt' => 'content']])->url();
        chmod($baseDir . '/directory/file.txt', 0000);
        chmod($baseDir . '/directory', 0000);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expecting value to be writable');
        Assert::writable($baseDir . $path);
    }
}
