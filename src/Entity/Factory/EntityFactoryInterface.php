<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface EntityFactoryInterface
{
    public function setEntityManager(EntityManagerInterface $entityManager
    ): EntityFactoryInterface;

    /**
     * Get an instance of the specific Entity Factory for a specified Entity
     *
     * Not type hinting the return because the whole point of this is to have an entity specific method, which we
     * can't hint for
     *
     * @param string $entityFqn
     *
     * @return mixed
     */
    public function createFactoryForEntity(string $entityFqn);

    public function getEntity(string $className);

    /**
     * Build a new entity with the validator factory preloaded
     *
     * Optionally pass in an array of property=>value
     *
     * @param string                           $entityFqn
     *
     * @param DataTransferObjectInterface|null $dto
     *
     * @return mixed
     */
    public function create(string $entityFqn, DataTransferObjectInterface $dto = null);

    /**
     * Take an already instantiated Entity and perform the final initialisation steps
     *
     * @param EntityInterface $entity
     */
    public function initialiseEntity(EntityInterface $entity): void;
}