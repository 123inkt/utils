<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Arrays;
use DR\Utils\Assert;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Accessory\AccessoryArrayListType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * Extension to ensure Arrays::isList the array is narrowed to list<V> while preserving value type information
 */
class AssertIsListReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Assert::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'isList';
    }

    /**
     * @inheritDoc
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        $arg       = Arrays::first($methodCall->getArgs());
        $valueType = $scope->getType($arg->value)->getIterableValueType();

        return TypeCombinator::intersect(new ArrayType(new IntegerType(), $valueType), new AccessoryArrayListType());
    }
}
