<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class EntitySaverFactory
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntitySaver
     */
    protected $genericEntitySaver;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Gets the Entity Specific Saver if one is defined, otherwise the standard Entity Saver
     *
     * @param EntityInterface $entity
     *
     * @return EntitySaverInterface
     */
    public function getSaverForEntity(
        EntityInterface $entity
    ): EntitySaverInterface {
        $saverFqn = $this->getSaverFqn($entity);
        if (class_exists($saverFqn)) {
            return new $saverFqn($this->entityManager);
        }
        if (null === $this->genericEntitySaver) {
            $this->genericEntitySaver = new EntitySaver($this->entityManager);
        }

        return $this->genericEntitySaver;
    }
}
