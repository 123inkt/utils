<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Arrays;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Accessory\AccessoryArrayListType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;
use PHPStan\TrinaryLogic;

/**
 * Resolves the precise item type for {@see Arrays::flatten()}, which cannot be expressed with a native
 * (recursive) generic type: nested arrays are recursively unwrapped down to their non-array leaf values.
 */
class ArraysFlattenReturnExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Arrays::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'flatten';
    }

    /**
     * @inheritDoc
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        [$items] = $methodCall->getArgs();

        // guard against argument unpacking (e.g. `Arrays::flatten(...$data)`), whose value type describes the
        // spread expression itself rather than the single `array $array` parameter
        if ($items->unpack) {
            return $methodReflection->getVariants()[0]->getReturnType();
        }

        $inputType = $scope->getType($items->value);

        // `flatten()`'s native `array` parameter already guarantees an array-like type, so unwrap every shape it
        // could be (e.g. each union member, and `list<T>`, which PHPStan represents as an intersection of
        // `array<int, T>` and an accessory list type)
        /** @var array<ArrayType|ConstantArrayType> $arrayTypes */
        $arrayTypes = $inputType->getArrays();
        // @codeCoverageIgnoreStart
        if ($arrayTypes === []) {
            return $inputType;
        }
        // @codeCoverageIgnoreEnd

        $resultTypes = array_map($this->flattenArrayType(...), $arrayTypes);

        return count($resultTypes) === 1 ? $resultTypes[0] : TypeCombinator::union(...$resultTypes);
    }

    /**
     * Resolves the underlying array type(s) (in case of a union) for the given type, or null when the type isn't
     * guaranteed to be an array (e.g. a union that also contains non-array members).
     *
     * @return array<ArrayType|ConstantArrayType>|null
     */
    private function resolveArrays(Type $type): ?array
    {
        if ($type->isArray()->yes() === false) {
            return null;
        }

        /** @var array<ArrayType|ConstantArrayType> $arrays */
        $arrays = $type->getArrays();

        return $arrays;
    }

    /**
     * Flattens a single array shape into its resulting {@see Arrays::flatten()} return type.
     */
    private function flattenArrayType(ArrayType|ConstantArrayType $arrayType): Type
    {
        $leafTypes = $this->collectLeafTypes($arrayType);

        // literal arrays are flattened into an exact tuple, preserving each leaf's precise type
        if (count($arrayType->getConstantArrays()) === 1) {
            return new ConstantArrayType(
                array_map(static fn (int $index): ConstantIntegerType => new ConstantIntegerType($index), array_keys($leafTypes)),
                array_values($leafTypes),
                [count($leafTypes)],
                [],
                TrinaryLogic::createYes()
            );
        }

        $itemType = count($leafTypes) > 0 ? TypeCombinator::union(...$leafTypes) : $arrayType->getItemType();

        // Arrays::flatten() always rebuilds the array via `$result[] = $item`, so the result is always a list
        return TypeCombinator::intersect(new ArrayType(new IntegerType(), $itemType), new AccessoryArrayListType());
    }

    /**
     * Recursively unwraps nested arrays into an ordered list of their non-array leaf types.
     *
     * @return Type[]
     */
    private function collectLeafTypes(Type $type): array
    {
        // definitely not an array (this also covers pure scalar/object unions, e.g. `int|string`) => single opaque leaf
        if ($type->isArray()->no()) {
            return [$type];
        }

        // a union that *might* be an array (e.g. `string|array{1}`) can't be resolved via `getArrays()`, which
        // returns an empty result for such "maybe" unions, so split and recurse per member instead
        if ($type instanceof UnionType) {
            $leafTypes = [];
            foreach ($type->getTypes() as $memberType) {
                array_push($leafTypes, ...$this->collectLeafTypes($memberType));
            }

            return $leafTypes;
        }

        $arrayTypes = $this->resolveArrays($type);
        if ($arrayTypes === null) {
            return [$type];
        }

        $leafTypes = [];
        foreach ($arrayTypes as $arrayType) {
            $constantArrays = $arrayType->getConstantArrays();
            $valueTypes = count($constantArrays) === 1 ? $constantArrays[0]->getValueTypes() : [$arrayType->getItemType()];

            foreach ($valueTypes as $valueType) {
                array_push($leafTypes, ...$this->collectLeafTypes($valueType));
            }
        }

        return $leafTypes;
    }
}
