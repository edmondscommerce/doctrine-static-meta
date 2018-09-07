<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

class RepositoryFactory
{
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager, NamespaceHelper $namespaceHelper)
    {
        $this->entityManager   = $entityManager;
        $this->namespaceHelper = $namespaceHelper;
    }

    public function getRepository(string $entityFqn): EntityRepositoryInterface
    {
        $repositoryFqn = $this->namespaceHelper->getRepositoryqnFromEntityFqn($entityFqn);

        return new $repositoryFqn($this->entityManager);
    }
}
