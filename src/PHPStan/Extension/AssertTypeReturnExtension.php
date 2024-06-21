<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;

class AssertTypeReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function __construct(private readonly TypeNarrower $typeNarrower)
    {
    }

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
