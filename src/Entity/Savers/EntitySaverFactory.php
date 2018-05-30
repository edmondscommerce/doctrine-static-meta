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
    /**
     * @var EntitySaver
     */
    protected $genericSaver;

    public function __construct(EntityManager $entityManager, EntitySaver $genericSaver)
    {
        $this->entityManager = $entityManager;
        $this->genericSaver = $genericSaver;
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

        return $this->genericSaver;
    }

    /**
     * Get the fully qualified name of the saver for the entity we are testing.
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    protected function getSaverFqn(
        EntityInterface $entity
    ): string {

        return \str_replace(
                   'Entities',
                   'Entity\\Savers',
                   \get_class($entity)
               ).'Saver';
    }
}
