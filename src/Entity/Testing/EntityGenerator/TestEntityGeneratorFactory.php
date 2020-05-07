<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\TestConfigurationException;
use EdmondsCommerce\DoctrineStaticMeta\RelationshipHelper;
use RuntimeException;

use function class_exists;
use function defined;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestEntityGeneratorFactory
{
    /**
     * @var EntitySaverFactory
     */
    protected EntitySaverFactory $entitySaverFactory;
    /**
     * @var EntityDataValidatorFactory
     */
    protected EntityDataValidatorFactory $entityValidatorFactory;
    /**
     * @var array
     */
    protected ?array $fakerDataProviderClasses;
    /**
     * @var float|null
     */
    protected ?float $seed;
    /**
     * @var EntityFactoryInterface|null
     */
    protected ?EntityFactoryInterface $entityFactory;
    /**
     * @var DtoFactory
     */
    private DtoFactory $dtoFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var NamespaceHelper
     */
    private NamespaceHelper $namespaceHelper;
    /**
     * @var FakerDataFillerFactory
     */
    private FakerDataFillerFactory $fakerDataFillerFactory;
    /**
     * @var RelationshipHelper
     */
    private RelationshipHelper $relationshipHelper;

    public function __construct(
        EntitySaverFactory $entitySaverFactory,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        EntityManagerInterface $entityManager,
        NamespaceHelper $namespaceHelper,
        FakerDataFillerFactory $fakerDataFillerFactory,
        RelationshipHelper $relationshipHelper,
        array $fakerDataProviderClasses = null,
        ?float $seed = null
    ) {
        $this->entitySaverFactory       = $entitySaverFactory;
        $this->entityFactory            = $entityFactory;
        $this->dtoFactory               = $dtoFactory;
        $this->entityManager            = $entityManager;
        $this->namespaceHelper          = $namespaceHelper;
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;
        $this->seed                     = $seed;
        $this->fakerDataFillerFactory   = $fakerDataFillerFactory;
        $this->relationshipHelper       = $relationshipHelper;
        $this->fakerDataFillerFactory->setSeed($seed);
        $this->fakerDataFillerFactory->setFakerDataProviders($fakerDataProviderClasses);
        $this->ensureMetaDataLoaded();
    }

    private function ensureMetaDataLoaded(): void
    {
        $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    public function createForEntityFqn(
        string $entityFqn,
        EntityManagerInterface $entityManager = null
    ): TestEntityGenerator {
        $this->fakerDataFillerFactory->setFakerDataProviders(
            $this->fakerDataProviderClasses ?? $this->getFakerDataProvidersFromEntityFqn($entityFqn)
        );

        return new TestEntityGenerator(
            $this->getEntityDsm($entityFqn),
            $this->entityFactory,
            $this->dtoFactory,
            $this,
            $this->getFakerDataFillerForEntityFqn($entityFqn),
            $entityManager ?? $this->entityManager,
            $this->relationshipHelper
        );
    }

    /**
     * Get the list of Faker data providers for the project
     *
     * By convention this is stored as a constant array on the project level AbstractEntityTest and is generated as
     * part of the DSM code generation
     *
     * This method will throw detailed exceptions if the abstract entity test is not found
     *
     * @param string $entityFqn
     *
     * @return array|string[]
     * @throws TestConfigurationException
     */
    private function getFakerDataProvidersFromEntityFqn(string $entityFqn): array
    {
        $projectRootNamespace = $this->namespaceHelper->getProjectNamespaceRootFromEntityFqn($entityFqn);
        $abstractTestFqn      = $this->namespaceHelper->tidy(
            $projectRootNamespace . '\\Entities\\AbstractEntityTest'
        );
        if (!class_exists($abstractTestFqn)) {
            throw new TestConfigurationException(<<<TEXT
Failed finding the AbstractEntityTest: $abstractTestFqn

This could means that your composer configuration is not correct with regards to 
including the abstract entity test that has all the definitions for faker data

You need something that looks like:

```
  "autoload-dev": {
    "psr-4": {
      "My\\Project\\": [
        "tests/"
      ],
      "My\\Entities\\Assets\\": [
        "vendor/my/entities/tests/Assets/"
      ]
    },
    "files": [
      "vendor/my/entities/tests/Entities/AbstractEntityTest.php"
    ]
  },
```

TEXT
            );
        }
        if (!defined($abstractTestFqn . '::FAKER_DATA_PROVIDERS')) {
            throw new TestConfigurationException(<<<TEXT
Your AbstractEntityTest ($abstractTestFqn) does not have the FAKER_DATA_PROVIDERS constant.
 
This means that you will not get any custom faker data which is essential to ensure 
your generated entities can pass their own validation and be persisted

TEXT
            );
        }

        return $abstractTestFqn::FAKER_DATA_PROVIDERS;
    }

    private function getEntityDsm(string $entityFqn): DoctrineStaticMeta
    {
        /**
         * @var DoctrineStaticMeta $dsm
         */
        $dsm      = $entityFqn::getDoctrineStaticMeta();
        $metaData = $this->entityManager->getMetadataFactory()->getMetadataFor($entityFqn);
        if ($metaData instanceof ClassMetadata) {
            $dsm->setMetaData($metaData);

            return $dsm;
        }
        throw new RuntimeException('$metaData is not an instance of ClassMetadata');
    }

    private function getFakerDataFillerForEntityFqn(string $entityFqn): FakerDataFillerInterface
    {
        return $this->fakerDataFillerFactory->getInstanceFromEntityFqn($entityFqn);
    }

    /**
     * @param float $seed
     *
     * @return TestEntityGeneratorFactory
     */
    public function setSeed(float $seed): TestEntityGeneratorFactory
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * @param array $fakerDataProviderClasses
     *
     * @return TestEntityGeneratorFactory
     */
    public function setFakerDataProviderClasses(array $fakerDataProviderClasses): TestEntityGeneratorFactory
    {
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;

        return $this;
    }
}
