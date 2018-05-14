<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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
     * @param IdFieldInterface $entity
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function save(IdFieldInterface $entity): void
    {
        $this->saveAll([$entity]);
    }

    /**
     *
     *
     * @param array|IdFieldInterface[] $entities *
     */
    public function saveAll(array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     *
     */
    public function remove(IdFieldInterface $entity): void
    {
        $this->removeAll([$entity]);
    }

    /**
     * @param array|IdFieldInterface[] $entities
     *
     */
    public function removeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }
}
