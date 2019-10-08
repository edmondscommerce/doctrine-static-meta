<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\AbstractTestFakerDataProviderUpdater;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use RuntimeException;
use ts\Reflection\ReflectionClass;

use function realpath;
use function str_replace;

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
     * @var string
     */
    protected $projectRootNamespace = '';
    /**
     * @var AbstractTestFakerDataProviderUpdater
     */
    private $abstractTestFakerDataProviderUpdater;
    /**
     * @var string
     */
    private $pathToProjectRoot;
    /**
     * @var DtoCreator
     */
    private $dtoCreator;

    public function __construct(
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper,
        AbstractTestFakerDataProviderUpdater $abstractTestFakerDataProviderUpdater,
        Config $config,
        DtoCreator $dtoCreator
    ) {
        $this->codeHelper                           = $codeHelper;
        $this->namespaceHelper                      = $namespaceHelper;
        $this->abstractTestFakerDataProviderUpdater = $abstractTestFakerDataProviderUpdater;
        $this->pathToProjectRoot                    = $config::getProjectRootDirectory();
        $this->setProjectRootNamespace($this->namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->dtoCreator = $dtoCreator;
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return $this
     */
    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->projectRootNamespace = rtrim($projectRootNamespace, '\\');

        return $this;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $realPath = realpath($pathToProjectRoot);
        if (false === $realPath) {
            throw new RuntimeException('Invalid path to project root ' . $pathToProjectRoot);
        }
        $this->pathToProjectRoot = $realPath;

        return $this;
    }

    public function setEntityHasEmbeddable(string $entityFqn, string $embeddableTraitFqn): void
    {
        $entityReflection          = new ReflectionClass($entityFqn);
        $entity                    = PhpClass::fromFile($entityReflection->getFileName());
        $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
        $entityInterfaceReflection = new ReflectionClass($entityInterfaceFqn);
        $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
        $embeddableReflection      = new ReflectionClass($embeddableTraitFqn);
        $trait                     = PhpTrait::fromFile($embeddableReflection->getFileName());
        $interfaceFqn              = str_replace(
            '\Traits\\',
            '\Interfaces\\',
            $embeddableTraitFqn
        );
        $interfaceFqn              = $this->namespaceHelper->swapSuffix($interfaceFqn, 'Trait', 'Interface');
        $interfaceReflection       = new ReflectionClass($interfaceFqn);
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
        $this->dtoCreator->setNewObjectFqnFromEntityFqn($entityFqn)
                         ->setProjectRootDirectory($this->pathToProjectRoot)
                         ->setProjectRootNamespace($this->projectRootNamespace)
                         ->createTargetFileObject()
                         ->write();
    }
}
