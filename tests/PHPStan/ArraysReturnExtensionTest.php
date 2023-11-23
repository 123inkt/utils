<?php
declare(strict_types=1);

namespace DR\Utils\Tests\PHPStan;

use DR\Utils\PHPStan\ArraysReturnExtension;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArraysReturnExtension::class)]
class ArraysReturnExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public static function dataFileAsserts(): iterable
    {
        yield from self::gatherAssertTypes(__DIR__ . '/data/ArraysReturnAssertions.php');
    }

    /**
     * @dataProvider dataFileAsserts
     *
     * @param mixed ...$args
     */
    public function testFileAsserts(string $assertType, string $file, ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 2) . '/extension.neon'];
    }
}
