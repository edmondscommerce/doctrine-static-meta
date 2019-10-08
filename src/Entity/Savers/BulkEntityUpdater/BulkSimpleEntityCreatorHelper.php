<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater;

interface BulkSimpleEntityCreatorHelper
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
}
