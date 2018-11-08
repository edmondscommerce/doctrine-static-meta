<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory;

class TestEntityGeneratorFactory
{
    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;
    /**
     * @var EntityDataValidatorFactory
     */
    protected $entityValidatorFactory;
    /**
     * @var array
     */
    protected $fakerDataProviderClasses;
    /**
     * @var float|null
     */
    protected $seed;
    /**
     * @var EntityFactoryInterface|null
     */
    protected $entityFactory;
    /**
     * @var DtoFactory
     */
    private $dtoFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;
    /**
     * @var FakerDataFillerFactory
     */
    private $fakerDataFillerFactory;

    public function __construct(
        EntitySaverFactory $entitySaverFactory,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        EntityManagerInterface $entityManager,
        NamespaceHelper $namespaceHelper,
        FakerDataFillerFactory $fakerDataFillerFactory,
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
            $entityManager ?? $this->entityManager
        );
    }

    /**
     * Get the list of Faker data providers for the project
     *
     * @param string $entityFqn
     *
     * @return array|string[]
     */
    private function getFakerDataProvidersFromEntityFqn(string $entityFqn): array
    {
        $projectRootNamespace = $this->namespaceHelper->getProjectNamespaceRootFromEntityFqn($entityFqn);
        $abstractTestFqn      = $this->namespaceHelper->tidy(
            $projectRootNamespace . '\\Entities\\AbstractEntityTest'
        );
        if (!\class_exists($abstractTestFqn)) {
            return [];
        }
        if (!\defined($abstractTestFqn . '::FAKER_DATA_PROVIDERS')) {
            return [];
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
        throw new \RuntimeException('$metaData is not an instance of ClassMetadata');
    }

    private function getFakerDataFillerForEntityFqn(string $entityFqn): FakerDataFiller
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
