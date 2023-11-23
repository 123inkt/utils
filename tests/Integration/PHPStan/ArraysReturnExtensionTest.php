<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan;

use DR\Utils\PHPStan\ArraysReturnExtension;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArraysReturnExtension::class)]
class ArraysReturnExtensionTest extends TypeInferenceTestCase
{
    public function testFileAsserts(): void
    {
        $results = self::gatherAssertTypes(__DIR__ . '/data/ArraysReturnAssertions.php');
        foreach ($results as $result) {
            $this->assertFileAsserts(...$result);
        }
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [dirname(__DIR__, 3) . '/extension.neon'];
    }
}
