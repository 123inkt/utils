<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan;

use DR\Utils\Arrays;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

class ArraysReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function __construct(private readonly TypeStringResolver $typeStringResolver)
    {
    }

    public function getClass(): string
    {
        return Arrays::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'removeTypes';
    }

    /**
     * @inheritDoc
     * @throws ShouldNotHappenException
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        [$items, $disallowedTypes] = $methodCall->getArgs();

        /** @var ArrayType $arrayType */
        $arrayType = $scope->getType($items->value);
        $itemsType = $arrayType->getItemType();
        $types     = $itemsType instanceof UnionType ? $itemsType->getTypes() : [$itemsType];

        // convert the disallowed types as string to phpstan types
        $disallowedStanTypes = [];
        foreach ($disallowedTypes->value->items as $item) {
            $disallowedStanTypes[] = $this->typeStringResolver->resolve($item->value->value);
        }

        $allowedStanTypes = [];
        foreach ($types as $type) {
            foreach ($disallowedStanTypes as $disallowedStanType) {
                if ($disallowedStanType->isSuperTypeOf($type)->yes()) {
                    continue 2;
                }
            }
            $allowedStanTypes[] = $type;
        }

        // all types are disallowed, will result in empty array
        if (count($allowedStanTypes) === 0) {
            return new ConstantArrayType([], []);
        }

        return new ArrayType(
            $arrayType->getKeyType(),
            count($allowedStanTypes) > 1 ? new UnionType($allowedStanTypes) : $allowedStanTypes[0]
        );
    }
}
