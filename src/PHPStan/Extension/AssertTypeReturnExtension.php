<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Lib\AssertTypeMethodTypeNarrower;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;

class AssertTypeReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    /**
     * @codeCoverageIgnore Will only be hit during initialisation
     */
    public function __construct(private readonly AssertTypeMethodTypeNarrower $typeNarrower)
    {
    }

    /**
     * @codeCoverageIgnore Will only be hit during initialisation
     */
    public function getClass(): string
    {
        return Assert::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'type';
    }

    /**
     * @inheritDoc
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        [$item, $allowedTypes] = $methodCall->getArgs();

        return $this->typeNarrower->narrow($item, $allowedTypes, $scope);
    }
}
