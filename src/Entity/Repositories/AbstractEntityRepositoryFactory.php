<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityRepositoryFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * AbstractRepositoryFactory constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager   = $entityManager;
        $this->reflectionClass = new \ReflectionClass($this);
    }

    public function getRepository()
    {
        if (null === $this->entityRepository) {
            $entityName     = $this->getEntityFqn();
            $repositoryName = $this->getRepositoryName();
            $metaData       = $this->entityManager->getClassMetadata($entityName);

            $this->entityRepository = new $repositoryName($this->entityManager, $metaData);
        }

        return $this->entityRepository;
    }

    protected function getEntityFqn()
    {
        $repositoryFqn = $this->reflectionClass->getName();
        return \str_replace(
            ['Entity\\Repositories', 'RepositoryFactory'],
            ['Entities', ''],
            $repositoryFqn
        );
    }

    protected function getRepositoryName()
    {
        $repositoryName = $this->reflectionClass->getShortName();
        return \str_replace(
            'Factory',
            '',
            $repositoryName
        );
    }
}