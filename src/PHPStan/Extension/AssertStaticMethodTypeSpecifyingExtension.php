<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Assert;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;

class AssertStaticMethodTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function getClass(): string
    {
        return Assert::class;
    }

    public function isStaticMethodSupported(MethodReflection $staticMethodReflection, StaticCall $node, TypeSpecifierContext $context): bool
    {
        return $staticMethodReflection->getName() === 'notNull';
    }

    public function specifyTypes(
        MethodReflection $staticMethodReflection,
        StaticCall $node,
        Scope $scope,
        TypeSpecifierContext $context
    ): SpecifiedTypes {
        $expression = self::createExpression($scope, $node->getArgs());
        if ($expression === null) {
            return new SpecifiedTypes([], []);
        }

        return $this->typeSpecifier
            ->specifyTypesInCondition(
                $scope,
                $expression,
                TypeSpecifierContext::createTruthy(),
            )
            ->setRootExpr($expression);
    }

    /**
     * @param Arg[] $args
     */
    private static function createExpression(
        Scope $scope,
        array $args
    ): ?Expr {
        $resolver   = static fn(Scope $scope, Arg $expected, Arg $actual): Identical => new Identical(
            $expected->value,
            new FuncCall(new Name('is_null'), [$actual]),
        );
        $expression = $resolver($scope, $args[0], $args[1]);
        if ($expression === null) {
            return null;
        }

        $expression = new BooleanNot($expression);

        return $expression;
    }
}
