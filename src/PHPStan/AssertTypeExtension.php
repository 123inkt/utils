<?php
declare(strict_types=1);

namespace DR\Utils\PHPStan;

use DR\Utils\Assert;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

class AssertTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function __construct(private readonly TypeStringResolver $typeStringResolver)
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
     * @throws ShouldNotHappenException
     */
    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        [$item, $allowedTypes] = $methodCall->getArgs();
        $types = $this->getTypesForArg($item, $scope);

        // convert the disallowed types as string to phpstan types
        $allowedStanTypes = TypeUtil::getTypesFromStringArray($this->typeStringResolver, $allowedTypes);

        foreach ($types as $index => $type) {
            $match = false;
            foreach ($allowedStanTypes as $allowedStanType) {
                if ($allowedStanType->isSuperTypeOf($type)->yes()) {
                    $match = true;
                    break;
                }
            }
            if ($match === false) {
                unset($types[$index]);
            }
        }

        return match (true) {
            count($types) === 0 => new NeverType(),
            count($types) === 1 => reset($types),
            default             => new UnionType($types),
        };
    }

    /**
     * @return Type[]
     */
    private function getTypesForArg(Arg $item, Scope $scope): array
    {
        $type = $scope->getType($item->value);

        return $type instanceof UnionType ? $type->getTypes() : [$type];
    }
}
