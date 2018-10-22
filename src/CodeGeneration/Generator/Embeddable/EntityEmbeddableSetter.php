<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\AbstractTestFakerDataProviderUpdater;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
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
    /**
     * @var AbstractTestFakerDataProviderUpdater
     */
    private $abstractTestFakerDataProviderUpdater;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var string
     */
    private $pathToProjectRoot;

    public function __construct(
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper,
        AbstractTestFakerDataProviderUpdater $abstractTestFakerDataProviderUpdater,
        Config $config
    ) {
        $this->codeHelper                           = $codeHelper;
        $this->namespaceHelper                      = $namespaceHelper;
        $this->abstractTestFakerDataProviderUpdater = $abstractTestFakerDataProviderUpdater;
        $this->pathToProjectRoot                    = $config::getProjectRootDirectory();
    }


    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $realPath = \realpath($pathToProjectRoot);
        if (false === $realPath) {
            throw new \RuntimeException('Invalid path to project root ' . $pathToProjectRoot);
        }
        $this->pathToProjectRoot = $realPath;

        return $this;
    }

    public function setEntityHasEmbeddable(string $entityFqn, string $embeddableTraitFqn): void
    {
        $entityReflection          = new \ts\Reflection\ReflectionClass($entityFqn);
        $entity                    = PhpClass::fromFile($entityReflection->getFileName());
        $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
        $entityInterfaceReflection = new \ts\Reflection\ReflectionClass($entityInterfaceFqn);
        $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
        $embeddableReflection      = new \ts\Reflection\ReflectionClass($embeddableTraitFqn);
        $trait                     = PhpTrait::fromFile($embeddableReflection->getFileName());
        $interfaceFqn              = \str_replace(
            '\Traits\\',
            '\Interfaces\\',
            $embeddableTraitFqn
        );
        $interfaceFqn              = $this->namespaceHelper->swapSuffix($interfaceFqn, 'Trait', 'Interface');
        $interfaceReflection       = new \ts\Reflection\ReflectionClass($interfaceFqn);
        $interface                 = PhpInterface::fromFile($interfaceReflection->getFileName());
        $entity->addTrait($trait);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
        $entityInterface->addInterface($interface);
        $this->codeHelper->generate($entityInterface, $entityInterfaceReflection->getFileName());
        $this->abstractTestFakerDataProviderUpdater->updateFakerProviderArrayWithEmbeddableFakerData(
            $this->pathToProjectRoot,
            $embeddableTraitFqn,
            $entityFqn
        );
    }
}
