<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;

class FakerDataFillerFactory
{
    /**
     * @var array
     */
    private $instances = [];
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;
    /**
     * @var array
     */
    private $fakerDataProviders;
    /**
     * @var float|null
     */
    private $seed;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
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

    public function getInstanceFromDataTransferObjectFqn(string $dtoFqn): FakerDataFiller
    {
        $entityFqn = $this->namespaceHelper->getEntityFqnFromEntityDtoFqn($dtoFqn);

        return $this->getInstanceFromEntityFqn($entityFqn);
    }

    public function getInstanceFromEntityFqn(string $entityFqn): FakerDataFiller
    {
        $dsm = $entityFqn::getDoctrineStaticMeta();

        return $this->getInstanceFromDsm($dsm);
    }

    public function getInstanceFromDsm(DoctrineStaticMeta $doctrineStaticMeta)
    {
        $entityFqn = $doctrineStaticMeta->getReflectionClass()->getName();
        if (array_key_exists($entityFqn, $this->instances)) {
            return $this->instances[$entityFqn];
        }
        if (null === $this->fakerDataProviders) {
            throw new \RuntimeException('You must call setFakerDataProviders before trying to get an instance');
        }

        $this->instances[$entityFqn] = new FakerDataFiller(
            $this,
            $doctrineStaticMeta,
            $this->namespaceHelper,
            $this->fakerDataProviders,
            $this->seed
        );

        return $this->instances[$entityFqn];
    }
}