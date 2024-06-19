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

class AssertTypeMethodTypeNarrower
{
    public function __construct(private readonly TypeStringResolver $typeStringResolver)
    {
    }

    public function narrow(Arg $item, Arg $allowedTypes, Scope $scope): Type
    {
        // convert the disallowed types as string to phpstan types
        $allowedStanTypes = $this->getTypesFromStringArray($allowedTypes);

        return TypeCombinator::intersect($scope->getType($item->value), $allowedStanTypes);
    }

    /**
     * @param Arg $arg arg of type Array
     */
    public function getTypesFromStringArray(Arg $arg): Type
    {
        $argValue = $arg->value;
        assert($argValue instanceof Array_);

        $types = [];
        foreach ($argValue->items as $item) {
            if ($item?->value instanceof String_) {
                // type definition is string, convert to type object
                $types[] = $this->typeStringResolver->resolve($item->value->value);
            } elseif ($item?->value instanceof ClassConstFetch && $item->value->class instanceof Name) {
                // type definition is class-string, convert to type object
                $types[] = $this->typeStringResolver->resolve($item->value->class->toString());
            }
        }

        return match (true) {
            count($types) === 0 => new NeverType(),
            count($types) === 1 => reset($types),
            default             => new UnionType($types),
        };
    }
}
