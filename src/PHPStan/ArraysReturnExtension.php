<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan;

use DR\Utils\Arrays;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

/**
 * @codeCoverageIgnore - covered by integration test
 */
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
        $keysType  = $arrayType instanceof ConstantArrayType ? $arrayType->getKeyTypes() : [];
        $types     = $this->getItemTypes($arrayType);

        // convert the disallowed types as string to phpstan types
        $disallowedStanTypes = $this->getDisallowedTypes($disallowedTypes);

        $allowedStanTypes = [];
        foreach ($types as $index => $type) {
            foreach ($disallowedStanTypes as $disallowedStanType) {
                if ($disallowedStanType->isSuperTypeOf($type)->yes()) {
                    unset($keysType[$index]);
                    continue 2;
                }
            }
            $allowedStanTypes[] = $type;
        }

        // all types are disallowed, will result in empty array
        if (count($allowedStanTypes) === 0) {
            return new ConstantArrayType([], []);
        }

        if ($arrayType instanceof ConstantArrayType) {
            return new ConstantArrayType(array_values($keysType), $allowedStanTypes);
        }

        return new ArrayType(
            $arrayType->getKeyType(),
            count($allowedStanTypes) > 1 ? new UnionType($allowedStanTypes) : $allowedStanTypes[0]
        );
    }

    /**
     * @return Type[]
     */
    private function getDisallowedTypes(Arg $arrayArgument): array
    {
        /** @var Array_ $disallowedTypesValue */
        $disallowedTypesValue = $arrayArgument->value;
        if ($disallowedTypesValue->items === null) {
            return [];
        }

        $disallowedStanTypes = [];
        foreach ($disallowedTypesValue->items as $item) {
            if ($item?->value instanceof String_) {
                // type definition is string, convert to type object
                $disallowedStanTypes[] = $this->typeStringResolver->resolve($item->value->value);
            } elseif ($item?->value instanceof ClassConstFetch && $item->value->class instanceof Name) {
                // type definition is class-string, convert to type object
                $disallowedStanTypes[] = $this->typeStringResolver->resolve($item->value->class->toString());
            }
        }

        return $disallowedStanTypes;
    }

    /**
     * @return Type[]
     */
    private function getItemTypes(ArrayType $arrayType): array
    {
        if ($arrayType instanceof ConstantArrayType) {
            return $arrayType->getValueTypes();
        }
        $itemsType = $arrayType->getItemType();

        return $itemsType instanceof UnionType ? $itemsType->getTypes() : [$itemsType];
    }
}
