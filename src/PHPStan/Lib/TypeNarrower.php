<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan\Lib;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;

class TypeNarrower
{
    public function __construct(private readonly TypeStringResolver $typeStringResolver)
    {
    }

    public function narrow(Arg $item, Arg $allowedTypes, Scope $scope): Type
    {
        // convert the disallowed types as string to phpstan types
        $allowedStanTypes = $this->getTypesFromStringArray($allowedTypes);
        $type             = match (true) {
            count($allowedStanTypes) === 0 => new NeverType(),
            count($allowedStanTypes) === 1 => reset($allowedStanTypes),
            default                        => new UnionType($allowedStanTypes),
        };

        return TypeCombinator::intersect($scope->getType($item->value), $type);
    }

    /**
     * @param Arg $arg arg of type Array
     *
     * @return Type[]
     */
    public function getTypesFromStringArray(Arg $arg): array
    {
        /** @var Array_ $argValue */
        $argValue = $arg->value;
        $types    = [];
        foreach ($argValue->items as $item) {
            if ($item?->value instanceof String_) {
                // type definition is string, convert to type object
                $types[] = $this->typeStringResolver->resolve($item->value->value);
            } elseif ($item?->value instanceof ClassConstFetch && $item->value->class instanceof Name) {
                // type definition is class-string, convert to type object
                $types[] = $this->typeStringResolver->resolve($item->value->class->toString());
            }
        }

        return $types;
    }
}
