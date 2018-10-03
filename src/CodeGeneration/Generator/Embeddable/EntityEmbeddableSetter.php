<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Roave\BetterReflection\Reflection\ReflectionClass;

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
    /**
     * @var CodeGenClassTypeFactory
     */
    private $classTypeFactory;

    public function __construct(
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper,
        CodeGenClassTypeFactory $classTypeFactory
    ) {
        $this->codeHelper       = $codeHelper;
        $this->namespaceHelper  = $namespaceHelper;
        $this->classTypeFactory = $classTypeFactory;
    }

    public function setEntityHasEmbeddable(string $entityFqn, string $embeddableTraitFqn): void
    {
        $entityReflection          = ReflectionClass::createFromName($entityFqn);
        $entityClassType           = $this->classTypeFactory->createClassTypeFromBetterReflection($entityReflection);
        $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
        $entityInterfaceReflection = ReflectionClass::createFromName($entityInterfaceFqn);
        $entityInterfaceClassType  = $this->classTypeFactory->createClassTypeFromBetterReflection($entityInterfaceReflection);
        #$embeddableReflection      = new \ts\Reflection\ReflectionClass($embeddableTraitFqn);
        #$trait                     = ClassType::from($embeddableTraitFqn);
        $interfaceFqn = \str_replace(
            '\Traits\\',
            '\Interfaces\\',
            $embeddableTraitFqn
        );
        $interfaceFqn = $this->namespaceHelper->swapSuffix($interfaceFqn, 'Trait', 'Interface');
        $entityClassType->addTrait($embeddableTraitFqn);
        $this->codeHelper->generate($entityClassType, $entityReflection->getFileName());
        $entityInterfaceClassType->addImplement($interfaceFqn);
        $this->codeHelper->generate($entityInterfaceClassType, $entityInterfaceReflection->getFileName());
    }
}
