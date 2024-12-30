<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Assert;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
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
    private const METHODS = ['notNull', 'string'];

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
        return in_array($staticMethodReflection->getName(), self::METHODS, true);
    }

    public function specifyTypes(
        MethodReflection $staticMethodReflection,
        StaticCall $node,
        Scope $scope,
        TypeSpecifierContext $context
    ): SpecifiedTypes {
        $expression = self::createExpression($staticMethodReflection->getName(), $node->getArgs());
        if ($expression === null) {
            return new SpecifiedTypes([], []);
        }

        return $this->typeSpecifier
            ->specifyTypesInCondition($scope, $expression, TypeSpecifierContext::createTruthy())
            ->setRootExpr($expression);
    }

    /**
     * @param Arg[] $args
     */
    private static function createExpression(string $name, array $args): ?Expr
    {
        return match ($name) {
            'string'  => new FuncCall(new Name('is_string'), [$args[0]]),
            'notNull' => new BooleanNot(new Identical($args[0]->value, new ConstFetch(new Name('null')))),
            default   => null,
        };
    }
}
