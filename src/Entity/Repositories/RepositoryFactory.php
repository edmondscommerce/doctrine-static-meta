<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;

class RepositoryFactory
{
    /**
     * @var NamespaceHelper
     */
    protected NamespaceHelper $namespaceHelper;
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;
    /**
     * @var EntityFactoryInterface
     */
    private EntityFactoryInterface $entityFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        NamespaceHelper $namespaceHelper,
        EntityFactoryInterface $entityFactory
    ) {
        $this->entityManager   = $entityManager;
        $this->namespaceHelper = $namespaceHelper;
        $this->entityFactory   = $entityFactory;
    }

    public function getRepository(string $entityFqn): EntityRepositoryInterface
    {
        $repositoryFqn = $this->namespaceHelper->getRepositoryqnFromEntityFqn($entityFqn);

        return new $repositoryFqn($this->entityManager, $this->entityFactory, $this->namespaceHelper);
    }
}
