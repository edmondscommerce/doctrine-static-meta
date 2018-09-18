<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface BulkEntityUpdateHelper
{
    /**
     * Get the name of the table that entity data is being persisted to
     *
     * @return string
     */
    public function getTableName(): string;

    /**
     * Get the fully qualified name of the Entity we are bulk updating. Note, we can only bulk update one Entity type
     *
     * @return string
     */
    public function getEntityFqn(): string;

    /**
     * This method should take an Entity and then return an array which includes a key=>value array of the columns that
     * are required to be updated.
     *
     * The first key=>value must be the primary key column name and the primary key value
     *
     * @param EntityInterface $entity
     *
     * @return array
     */
    public function extract(EntityInterface $entity): array;
}