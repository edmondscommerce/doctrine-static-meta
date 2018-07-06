<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;

/**
 * Class EntityEmbeddableSetter
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class EntityEmbeddableSetter
{
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(CodeHelper $codeHelper, NamespaceHelper $namespaceHelper)
    {
        $this->codeHelper      = $codeHelper;
        $this->namespaceHelper = $namespaceHelper;
    }

    public function setEntityHasEmbeddable(string $entityFqn, string $embeddableTraitFqn)
    {
        $entityReflection          = new \ReflectionClass($entityFqn);
        $entity                    = PhpClass::fromFile($entityReflection->getFileName());
        $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
        $entityInterfaceReflection = new \ReflectionClass($entityInterfaceFqn);
        $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
        $embeddableReflection      = new \ReflectionClass($embeddableTraitFqn);
        $trait                     = PhpTrait::fromFile($embeddableReflection->getFileName());
        $interfaceFqn              = \str_replace(
            '\Traits\\',
            '\Interfaces\\',
            $embeddableTraitFqn
        );
        $interfaceFqn              = $this->namespaceHelper->swapSuffix($interfaceFqn, 'Trait', 'Interface');
        $interfaceReflection       = new \ReflectionClass($interfaceFqn);
        $interface                 = PhpInterface::fromFile($interfaceReflection->getFileName());
        $entity->addTrait($trait);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
        $entityInterface->addInterface($interface);
        $this->codeHelper->generate($entityInterface, $entityInterfaceReflection->getFileName());
    }
}
