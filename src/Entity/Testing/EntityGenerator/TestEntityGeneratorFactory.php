<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(
        EntitySaverFactory $entitySaverFactory,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        EntityManagerInterface $entityManager,
        array $fakerDataProviderClasses = [],
        ?float $seed = null
    ) {
        $this->entitySaverFactory       = $entitySaverFactory;
        $this->entityFactory            = $entityFactory;
        $this->dtoFactory               = $dtoFactory;
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;
        $this->seed                     = $seed;
        $this->entityManager            = $entityManager;
    }

    public function createForEntityFqn(
        string $entityFqn
    ): TestEntityGenerator {
        return new TestEntityGenerator(
            $this->getEntityDsm($entityFqn),
            $this->entityFactory,
            $this->dtoFactory,
            $this,
            $this->getFakerDataFillerForEntityFqn($entityFqn)
        );
    }

    private function getEntityDsm(string $entityFqn): DoctrineStaticMeta
    {
        /**
         * @var DoctrineStaticMeta $dsm
         */
        $dsm = $entityFqn::getDoctrineStaticMeta();
        if (null === $dsm->getMetaData()) {
            $dsm->setMetaData($this->entityManager->getMetadataFactory()->getMetadataFor($entityFqn));
        }

        return $dsm;
    }

    private function getFakerDataFillerForEntityFqn(string $entityFqn): FakerDataFiller
    {
        return new FakerDataFiller(
            $this->getEntityDsm($entityFqn),
            $this->fakerDataProviderClasses
        );
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
