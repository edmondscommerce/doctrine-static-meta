<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

interface EntitySaverInterface
{
    /**
     * @param IdFieldInterface $entity
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function save(IdFieldInterface $entity): void;

    /**
     * @param array|IdFieldInterface[] $entities
     *
     * @throws \ReflectionException
     */
    public function saveAll(array $entities): void;

    /**
     * @param IdFieldInterface $entity
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function remove(IdFieldInterface $entity): void;

    /**
     * @param array|IdFieldInterface[] $entities
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function removeAll(array $entities): void;
}
