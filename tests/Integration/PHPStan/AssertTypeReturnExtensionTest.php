<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan;

use DR\Utils\Assert;
use DR\Utils\PHPStan\ArraysReturnExtension;
use DR\Utils\PHPStan\AssertTypeExtension;
use DR\Utils\PHPStan\TypeUtil;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AssertTypeExtension::class)]
#[CoversClass(TypeUtil::class)]
class AssertTypeReturnExtensionTest extends TypeInferenceTestCase
{
    public function testFileAsserts(): void
    {
        $results = self::gatherAssertTypes(__DIR__ . '/data/AssertTypeAssertions.php');
        foreach ($results as $result) {
            $assertType = Assert::string(array_shift($result));
            $file       = Assert::string(array_shift($result));
            $this->assertFileAsserts($assertType, $file, ...$result);
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
