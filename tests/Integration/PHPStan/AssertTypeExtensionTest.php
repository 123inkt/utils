<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\AssertTypeReturnExtension;
use DR\Utils\PHPStan\Extension\AssertTypeTypeSpecifyingExtension;
use DR\Utils\PHPStan\Lib\AssertTypeMethodTypeNarrower;
use DR\Utils\PHPStan\Lib\TypeUtil;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AssertTypeReturnExtension::class)]
#[CoversClass(AssertTypeTypeSpecifyingExtension::class)]
#[CoversClass(AssertTypeMethodTypeNarrower::class)]
class AssertTypeExtensionTest extends TypeInferenceTestCase
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
