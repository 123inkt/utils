<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Type\Type;

class TypeUtil
{
    /**
     * @param Arg $arg arg of type Array
     *
     * @return Type[]
     */
    public static function getTypesFromStringArray(TypeStringResolver $typeStringResolver, Arg $arg): array
    {
        $argValue = $arg->value;
        assert($argValue instanceof Array_);

        $types = [];
        foreach ($argValue->items as $item) {
            if ($item?->value instanceof String_) {
                // type definition is string, convert to type object
                $types[] = $typeStringResolver->resolve($item->value->value);
            } elseif ($item?->value instanceof ClassConstFetch && $item->value->class instanceof Name) {
                // type definition is class-string, convert to type object
                $types[] = $typeStringResolver->resolve($item->value->class->toString());
            }
        }

        return $types;
    }
}
