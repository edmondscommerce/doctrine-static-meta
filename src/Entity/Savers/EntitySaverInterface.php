<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

interface EntitySaverInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function save(EntityInterface $entity): void;

    /**
     * @param array|EntityInterface[] $entities
     *
     * @throws \ReflectionException
     */
    public function saveAll(array $entities): void;

    /**
     * @param EntityInterface $entity
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function remove(EntityInterface $entity): void;

    /**
     * @param array|EntityInterface[] $entities
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function removeAll(array $entities): void;
}
