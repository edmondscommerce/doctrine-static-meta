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
            $entityFqn     = $this->getEntityFqn();
            $repositoryFqn = $this->getRepositoryFqn();
            $metaData      = $this->entityManager->getClassMetadata($entityFqn);

            $this->entityRepository = new $repositoryFqn($this->entityManager, $metaData);
        }

        return $this->entityRepository;
    }

    protected function getEntityFqn()
    {
        $repositoryFactoryFqn = $this->reflectionClass->getName();
        return '\\'.\str_replace(
            ['Entity\\Repositories', 'RepositoryFactory'],
            ['Entities', ''],
            $repositoryFactoryFqn
        );
    }

    protected function getRepositoryFqn()
    {
        $repositoryFactoryFqn = $this->reflectionClass->getName();
        return '\\'.\str_replace(
            'Factory',
            '',
            $repositoryFactoryFqn
        );
    }
}