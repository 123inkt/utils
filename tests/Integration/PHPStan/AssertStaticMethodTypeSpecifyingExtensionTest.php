<?php

declare(strict_types=1);

namespace DR\Utils\Tests\Integration\PHPStan;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Extension\AssertStaticMethodTypeSpecifyingExtension;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversClass(AssertStaticMethodTypeSpecifyingExtension::class)]
class AssertStaticMethodTypeSpecifyingExtensionTest extends TypeInferenceTestCase
{
    #[TestWith(['/data/AssertFalseTypeSpecifyingAssertions.php'])]
    #[TestWith(['/data/AssertTrueTypeSpecifyingAssertions.php'])]
    #[TestWith(['/data/AssertStringTypeSpecifyingAssertions.php'])]
    #[TestWith(['/data/AssertNotNullTypeSpecifyingAssertions.php'])]
    public function testFileAsserts(string $path): void
    {
        $results = self::gatherAssertTypes(__DIR__ . $path);
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
