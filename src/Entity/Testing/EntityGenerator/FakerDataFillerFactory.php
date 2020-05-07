<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use RuntimeException;

class FakerDataFillerFactory
{
    /**
     * @var array
     */
    private array $instances = [];
    /**
     * @var NamespaceHelper
     */
    private NamespaceHelper $namespaceHelper;
    /**
     * @var array
     */
    private array $fakerDataProviders;
    /**
     * @var float|null
     */
    private ?float $seed;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var array
     */
    private array $customFakerDataFillersFqns = [];

    public function __construct(NamespaceHelper $namespaceHelper, EntityManagerInterface $entityManager)
    {
        $this->namespaceHelper = $namespaceHelper;
        $this->entityManager   = $entityManager;
    }

    /**
     * Custom Faker Data Fillers are used to generate bespoke fake data on an Entity FQN basis
     *
     * The array should contain Entity FQNs => Custom Faker Data Filler FQN
     *
     * The custom faker data filler must implement of FakerDataFillerInterface and can extend FakerDataFiller
     *
     * @param array<string, string> $customFakerDataFillersFqns
     */
    public function setCustomFakerDataFillersFqns(array $customFakerDataFillersFqns): void
    {
        $this->customFakerDataFillersFqns = $customFakerDataFillersFqns;
    }

    /**
     * @param array $fakerDataProviders
     *
     * @return FakerDataFillerFactory
     */
    public function setFakerDataProviders(?array $fakerDataProviders): FakerDataFillerFactory
    {
        $this->fakerDataProviders = $fakerDataProviders;

        return $this;
    }

    /**
     * @param float $seed
     *
     * @return FakerDataFillerFactory
     */
    public function setSeed(?float $seed): FakerDataFillerFactory
    {
        $this->seed = $seed;

        return $this;
    }

    public function getInstanceFromDataTransferObjectFqn(string $dtoFqn): FakerDataFillerInterface
    {
        $entityFqn = $this->namespaceHelper->getEntityFqnFromEntityDtoFqn($dtoFqn);

        return $this->getInstanceFromEntityFqn($entityFqn);
    }

    public function getInstanceFromEntityFqn(string $entityFqn): FakerDataFillerInterface
    {
        $dsm = $entityFqn::getDoctrineStaticMeta();

        return $this->getInstanceFromDsm($dsm);
    }

    /**
     * This will return an instance of FakerDataFillerInterface
     *
     * If there has been configured a custom Faker Data Filler for the Entity FQN then an instance of that will be
     * returned, otherwise it will be the standard and generic Faker Data Filler
     *
     * If you want to register a custom faker data filler, you need to call setCustomFakerDataFillersFqns()
     *
     * @param DoctrineStaticMeta $doctrineStaticMeta
     *
     * @return FakerDataFillerInterface
     */
    public function getInstanceFromDsm(DoctrineStaticMeta $doctrineStaticMeta): FakerDataFillerInterface
    {
        $entityFqn = $doctrineStaticMeta->getReflectionClass()->getName();
        if (array_key_exists($entityFqn, $this->instances)) {
            return $this->instances[$entityFqn];
        }
        if (null === $this->fakerDataProviders) {
            throw new RuntimeException('You must call setFakerDataProviders before trying to get an instance');
        }
        $doctrineStaticMeta->setMetaData($this->entityManager->getMetadataFactory()->getMetadataFor($entityFqn));

        $fakerDataFillerFqn = $this->customFakerDataFillersFqns[$entityFqn] ?? FakerDataFiller::class;

        $this->instances[$entityFqn] = new $fakerDataFillerFqn(
            $this,
            $doctrineStaticMeta,
            $this->namespaceHelper,
            $this->fakerDataProviders,
            $this->seed
        );

        return $this->instances[$entityFqn];
    }
}
