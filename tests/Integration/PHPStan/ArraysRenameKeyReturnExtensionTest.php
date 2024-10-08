<?php
declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\ArraysRemoveTypesReturnExtension;
use DR\Utils\PHPStan\Extension\ArraysRenameKeyReturnExtension;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArraysRenameKeyReturnExtension::class)]
class ArraysRenameKeyReturnExtensionTest extends TypeInferenceTestCase
{
    public function testFileAsserts(): void
    {
        $results = self::gatherAssertTypes(__DIR__ . '/data/ArraysRenameKeyReturnAssertions.php');
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
