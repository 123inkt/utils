<?php

declare(strict_types=1);

namespace DR\Utils\PHPStan\Extension;

use DR\Utils\Assert;
use DR\Utils\PHPStan\Lib\TypeNarrower;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;

/**
 * Extension to ensure the types are narrowed to int|null for example:
 * <code>
 *     Assert::type($value, ['int', 'null']);
 * </code>
 * @see \PHPStan\Type\PHPUnit\Assert\AssertStaticMethodTypeSpecifyingExtension as an example
 */
class AssertTypeTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    /**
     * @codeCoverageIgnore Will only be hit during initialisation
     */
    public function __construct(private readonly TypeNarrower $typeNarrower)
    {
    }

    /**
     * @codeCoverageIgnore Will only be hit during initialisation
     */
    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function getClass(): string
    {
        return Assert::class;
    }

    /**
     * @inheritDoc
     */
    public function isStaticMethodSupported(MethodReflection $staticMethodReflection, StaticCall $node, TypeSpecifierContext $context): bool
    {
        return $staticMethodReflection->getName() === 'type';
    }

    public function specifyTypes(
        MethodReflection $staticMethodReflection,
        StaticCall $node,
        Scope $scope,
        TypeSpecifierContext $context
    ): SpecifiedTypes {
        [$item, $allowedTypes] = $node->getArgs();
        $type = $this->typeNarrower->narrow($item, $allowedTypes, $scope);

        return $this->typeSpecifier->create($item->value, $type, TypeSpecifierContext::createTruthy());
    }
}
