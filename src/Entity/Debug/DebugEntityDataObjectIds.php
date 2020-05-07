<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Debug;

trait DebugEntityDataObjectIds
{

    /**
     * When creating a new Entity, we track the increment to help with identifying Entities
     *
     * @param bool $created
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function initDebugIds(bool $created = false): void
    {
        $debugIdAsString = (string)$this->id;
        $debugObjectHash = spl_object_hash($this);
        if (false === $created) {
            return;
        }
        static $increment = 0;
        $debugCreationIncrement = ++$increment;
    }
}
