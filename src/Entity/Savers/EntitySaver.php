<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use InvalidArgumentException;
use function get_class;

/**
 * Class EntitySaver
 *
 * Generic Entity Saver
 *
 * Can be used to save any entities as required
 *
 * For Entity specific saving logic, you should create an Entity Specific Saver
 * that subclasses:
 * \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractEntitySpecificSaver
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Savers
 */
class EntitySaver implements EntitySaverInterface
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
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param EntityInterface $entity
     *
     */
    public function save(EntityInterface $entity): void
    {
        $this->saveAll([$entity]);
    }

    /**
     * @param array|EntityInterface[] $entities
     *
     */
    public function saveAll(array $entities): void
    {
        if ([] === $entities) {
            return;
        }
        foreach ($entities as $entity) {
            if (false === $entity instanceof EntityInterface) {
                throw new InvalidArgumentException(
                    'Found invalid $entity was not an EntityInterface, was ' . get_class($entity)
                );
            }
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param EntityInterface $entity
     */
    public function remove(EntityInterface $entity): void
    {
        $this->removeAll([$entity]);
    }

    /**
     * @param array|EntityInterface[] $entities
     */
    public function removeAll(array $entities): void
    {
        if ([] === $entities) {
            return;
        }
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
