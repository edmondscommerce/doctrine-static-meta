<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

abstract class AbstractSaver
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityFqn;

    /**
     * AbstractSaver constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param IdFieldInterface $entity
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function save(IdFieldInterface $entity): void
    {
        $this->saveAll([$entity]);
    }

    /**
     *
     *
     * @param array $entities
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function saveAll(array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function remove(IdFieldInterface $entity): void
    {
        $this->removeAll([$entity]);
    }

    /**
     * @param array $entities
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function removeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     * @return void
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function checkIsCorrectEntityType(IdFieldInterface $entity): void
    {
        $entityFqn = $this->getEntityFqn();

        if (! $entity instanceof $entityFqn) {
            $ref = new \ReflectionClass($entity);
            $msg = "[ {$ref->getName()} ] is not an instance of [ $entityFqn ]";
            throw new DoctrineStaticMetaException($msg);
        }
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getEntityFqn()
    {
        if (null === $this->entityFqn) {
            $ref = new \ReflectionClass($this);

            $saverNamespace  = $ref->getNamespaceName();
            $entityNamespace = \str_replace(
                'Entity\\Savers',
                'Entities',
                $saverNamespace
            );

            $saverClassName  = $ref->getShortName();
            $entityClassName = substr($saverClassName, 0, -5);

            $this->entityFqn = $entityNamespace.'\\'.$entityClassName;
        }

        return $this->entityFqn;
    }
}
