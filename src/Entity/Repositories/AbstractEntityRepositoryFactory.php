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
     * @var string
     */
    protected $repositoryFactoryFqn;

    /**
     * AbstractEntityRepositoryFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @throws \ReflectionException
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager        = $entityManager;
        $this->reflectionClass      = new \ReflectionClass($this);
        $this->repositoryFactoryFqn = $this->reflectionClass->getName();
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
        return '\\'.\str_replace(
            ['Entity\\Repositories', 'RepositoryFactory'],
            ['Entities', ''],
            $this->repositoryFactoryFqn
        );
    }

    protected function getRepositoryFqn()
    {
        return '\\'.\str_replace(
            'RepositoryFactory',
            'Repository',
            $this->repositoryFactoryFqn
        );
    }
}
