<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntitySaverCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUnitOfWorkHelperCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\Entity\Fixtures\EntityFixtureCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CreateEntityAction implements ActionInterface
{
    /**
     * @var EntityCreator
     */
    private $entityCreator;
    /**
     * @var AbstractEntityFactoryCreator
     */
    private $abstractEntityFactoryCreator;
    /**
     * @var EntityFactoryCreator
     */
    private $entityFactoryCreator;
    /**
     * @var EntityInterfaceCreator
     */
    private $entityInterfaceCreator;
    /**
     * @var AbstractEntityRepositoryCreator
     */
    private $abstractEntityRepositoryCreator;
    /**
     * @var EntityRepositoryCreator
     */
    private $entityRepositoryCreator;

    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var EntityFixtureCreator
     */
    private $entityFixtureCreator;
    /**
     * @var AbstractEntityTestCreator
     */
    private $abstractEntityTestCreator;
    /**
     * @var EntityTestCreator
     */
    private $entityTestCreator;
    /**
     * @var EntitySaverCreator
     */
    private $entitySaverCreator;
    /**
     * @var EntityUnitOfWorkHelperCreator
     */
    private $entityUnitOfWorkHelperCreator;

    /**
     * @var bool
     */
    private $generateSaver = false;
    /**
     * @var BootstrapCreator
     */
    private $bootstrapCreator;
    /**
     * @var DtoCreator
     */
    private $dataTransferObjectCreator;
    /**
     * @var EntityDtoFactoryCreator
     */
    private $entityDtoFactoryCreator;
    /**
     * @var EntityUpserterCreator
     */
    private $entityUpserterCreator;

    public function __construct(
        EntityCreator $entityCreator,
        AbstractEntityFactoryCreator $abstractEntityFactoryCreator,
        EntityFactoryCreator $entityFactoryCreator,
        EntityInterfaceCreator $entityInterfaceCreator,
        AbstractEntityRepositoryCreator $abstractEntityRepositoryCreator,
        EntityRepositoryCreator $entityRepositoryCreator,
        EntitySaverCreator $entitySaverCreator,
        EntityFixtureCreator $entityFixtureCreator,
        AbstractEntityTestCreator $abstractEntityTestCreator,
        BootstrapCreator $bootstrapCreator,
        EntityTestCreator $entityTestCreator,
        DtoCreator $dataTransferObjectCreator,
        EntityDtoFactoryCreator $entityDtoFactoryCreator,
        EntityUpserterCreator $entityUpserterCreator,
        EntityUnitOfWorkHelperCreator $entityUnitOfWorkHelperCreator
    ) {
        $this->entityCreator                   = $entityCreator;
        $this->abstractEntityFactoryCreator    = $abstractEntityFactoryCreator;
        $this->entityFactoryCreator            = $entityFactoryCreator;
        $this->entityInterfaceCreator          = $entityInterfaceCreator;
        $this->abstractEntityRepositoryCreator = $abstractEntityRepositoryCreator;
        $this->entityRepositoryCreator         = $entityRepositoryCreator;
        $this->entitySaverCreator              = $entitySaverCreator;
        $this->entityFixtureCreator            = $entityFixtureCreator;
        $this->abstractEntityTestCreator       = $abstractEntityTestCreator;
        $this->entityTestCreator               = $entityTestCreator;
        $this->bootstrapCreator                = $bootstrapCreator;
        $this->dataTransferObjectCreator       = $dataTransferObjectCreator;
        $this->entityDtoFactoryCreator         = $entityDtoFactoryCreator;
        $this->entityUpserterCreator           = $entityUpserterCreator;
        $this->entityUnitOfWorkHelperCreator = $entityUnitOfWorkHelperCreator;
    }

    public function setEntityFqn(string $entityFqn): self
    {
        $this->assertSingularEntityName($entityFqn);
        $this->entityFqn = $entityFqn;
        $this->entityFactoryCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityInterfaceCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityRepositoryCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entitySaverCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityFixtureCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityTestCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->dataTransferObjectCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityDtoFactoryCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityUpserterCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityUnitOfWorkHelperCreator->setNewObjectFqnFromEntityFqn($entityFqn);

        return $this;
    }

    private function assertSingularEntityName(string $entityFqn): void
    {
        $namespaceHelper = new NamespaceHelper();
        $shortName       = $namespaceHelper->getClassShortName($entityFqn);
        $singular        = ucfirst(MappingHelper::getSingularForFqn($entityFqn));
        if ($shortName !== $singular) {
            throw new \InvalidArgumentException(
                "Your Entity Name must be Singular, eg not $shortName but $singular"
            );
        }
    }

    public function setPrimaryKeyTraitFqn(string $primaryKeyTraitFqn)
    {
        $replaceIdFieldProcess = new ReplaceEntityIdFieldProcess();
        $replaceIdFieldProcess->setIdTraitFqn($primaryKeyTraitFqn);
        $this->entityCreator->setReplaceIdFieldProcess($replaceIdFieldProcess);
        $this->entityInterfaceCreator->setIsSettableUuid(
            \ts\stringContains($primaryKeyTraitFqn, 'Uuid')
        );
    }

    /**
     * Create all the Entity related code
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function run(): void
    {
        $this->entityCreator->createTargetFileObject($this->entityFqn)->write();

        $this->abstractEntityFactoryCreator->createTargetFileObject()->writeIfNotExists();

        $this->entityFactoryCreator->createTargetFileObject()->write();

        $this->entityInterfaceCreator->createTargetFileObject()->write();

        $this->abstractEntityRepositoryCreator->createTargetFileObject()->writeIfNotExists();

        $this->entityRepositoryCreator->createTargetFileObject()->write();

        if (true === $this->generateSaver) {
            $this->entitySaverCreator->createTargetFileObject()->write();
        }

        $this->entityFixtureCreator->createTargetFileObject()->write();

        $this->abstractEntityTestCreator->createTargetFileObject()->writeIfNotExists();

        $this->bootstrapCreator->createTargetFileObject()->writeIfNotExists();

        $this->entityTestCreator->createTargetFileObject()->write();

        $this->dataTransferObjectCreator->createTargetFileObject()->write();

        $this->entityDtoFactoryCreator->createTargetFileObject()->write();

        $this->entityUpserterCreator->createTargetFileObject()->write();

        $this->entityUnitOfWorkHelperCreator->createTargetFileObject()->write();
    }

    public function getCreatedEntityFilePath(): string
    {
        return $this->entityCreator->getTargetFile()->getPath();
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->entityCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityFactoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityFactoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityInterfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityRepositoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityRepositoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entitySaverCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityFixtureCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityTestCreator->setProjectRootNamespace($projectRootNamespace);
        $this->bootstrapCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityTestCreator->setProjectRootNamespace($projectRootNamespace);
        $this->dataTransferObjectCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityDtoFactoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityUpserterCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityUnitOfWorkHelperCreator->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->entityCreator->setProjectRootDirectory($projectRootDirectory);
        $this->abstractEntityFactoryCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityFactoryCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityInterfaceCreator->setProjectRootDirectory($projectRootDirectory);
        $this->abstractEntityRepositoryCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityRepositoryCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entitySaverCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityFixtureCreator->setProjectRootDirectory($projectRootDirectory);
        $this->abstractEntityTestCreator->setProjectRootDirectory($projectRootDirectory);
        $this->bootstrapCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityTestCreator->setProjectRootDirectory($projectRootDirectory);
        $this->dataTransferObjectCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityDtoFactoryCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityUpserterCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityUnitOfWorkHelperCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    /**
     * @param bool $generateSaver
     *
     * @return CreateEntityAction
     */
    public function setGenerateSaver(bool $generateSaver): CreateEntityAction
    {
        $this->generateSaver = $generateSaver;

        return $this;
    }
}
