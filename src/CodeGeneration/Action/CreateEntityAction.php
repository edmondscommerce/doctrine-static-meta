<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\EntityFixtures\EntityFixtureCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator;

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

    public function __construct(
        EntityCreator $entityCreator,
        AbstractEntityFactoryCreator $abstractEntityFactoryCreator,
        EntityFactoryCreator $entityFactoryCreator,
        EntityInterfaceCreator $entityInterfaceCreator,
        AbstractEntityRepositoryCreator $abstractEntityRepositoryCreator,
        EntityRepositoryCreator $entityRepositoryCreator,
        EntityFixtureCreator $entityFixtureCreator,
        AbstractEntityTestCreator $abstractEntityTestCreator,
        EntityTestCreator $entityTestCreator
    ) {
        $this->entityCreator                   = $entityCreator;
        $this->abstractEntityFactoryCreator    = $abstractEntityFactoryCreator;
        $this->entityFactoryCreator            = $entityFactoryCreator;
        $this->entityInterfaceCreator          = $entityInterfaceCreator;
        $this->abstractEntityRepositoryCreator = $abstractEntityRepositoryCreator;
        $this->entityRepositoryCreator         = $entityRepositoryCreator;
        $this->entityFixtureCreator            = $entityFixtureCreator;
        $this->abstractEntityTestCreator       = $abstractEntityTestCreator;
        $this->entityTestCreator               = $entityTestCreator;
    }

    public function setEntityFqn(string $entityFqn): self
    {
        $this->entityFqn = $entityFqn;
        $this->entityFactoryCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityInterfaceCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityRepositoryCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityFixtureCreator->setNewObjectFqnFromEntityFqn($entityFqn);
        $this->entityTestCreator->setNewObjectFqnFromEntityFqn($entityFqn);

        return $this;
    }

    public function setPrimaryKeyTraitFqn(string $primaryKeyTraitFqn)
    {
        $replaceIdFieldProcess = new ReplaceEntityIdFieldProcess();
        $replaceIdFieldProcess->setIdTraitFqn($primaryKeyTraitFqn);
        $this->entityCreator->setReplaceIdFieldProcess($replaceIdFieldProcess);
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

        $this->entityFixtureCreator->createTargetFileObject()->write();

        $this->abstractEntityTestCreator->createTargetFileObject()->writeIfNotExists();

        $this->entityTestCreator->createTargetFileObject()->write();


    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->entityCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityFactoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityFactoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityInterfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityRepositoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityRepositoryCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityFixtureCreator->setProjectRootNamespace($projectRootNamespace);
        $this->abstractEntityTestCreator->setProjectRootNamespace($projectRootNamespace);
        $this->entityTestCreator->setProjectRootNamespace($projectRootNamespace);


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
        $this->entityFixtureCreator->setProjectRootDirectory($projectRootDirectory);
        $this->abstractEntityTestCreator->setProjectRootDirectory($projectRootDirectory);
        $this->entityTestCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }
}