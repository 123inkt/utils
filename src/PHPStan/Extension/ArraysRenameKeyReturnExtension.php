<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Arrays;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;

class ArraysRenameKeyReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Arrays::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'renameKey';
    }

    /**
     * @inheritDoc
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        [$items, $fromKey, $toKey] = $methodCall->getArgs();

        $arrayType = $scope->getType($items->value);
        // @codeCoverageIgnoreStart
        if ($arrayType instanceof ArrayType === false && $arrayType instanceof ConstantArrayType === false) {
            return $arrayType;
        }
        // @codeCoverageIgnoreEnd

        $keyTypes = $arrayType->getKeyType()->getFiniteTypes();

        if ($fromKey->value instanceof String_ === false ||
            $toKey->value instanceof String_ === false ||
            count($keyTypes) === 0
        ) {
            return $scope->getType($items->value);
        }

        $fromKeyValue = $fromKey->value->value;
        $toKeyValue   = $toKey->value->value;

        $newKeyTypes = [];
        foreach ($keyTypes as $keyType) {
            if ([$fromKeyValue] === $keyType->getConstantScalarValues()) {
                $newKeyTypes[] = new ConstantStringType($toKeyValue);
            } else {
                $newKeyTypes[] = $keyType;
            }
        }

        return new ConstantArrayType($newKeyTypes, $arrayType->getValueTypes());
    }
}
