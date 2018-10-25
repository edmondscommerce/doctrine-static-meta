<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Debug;


trait DebugEntityDataObjectIds
{
    /**
     * The spl_object_hash
     *
     * @var string
     */
    private $debugObjectHash;

    /**
     * The UUID as a string, only for debugging purposes
     *
     * @var string
     */
    private $debugIdAsString;

    /**
     * A rough approximation of an auto incrementing ID - only for debugging purposes, no functional purpose
     *
     * @var int
     */
    private $debugCreationIncrement;

    /**
     * When creating a new Entity, we track the increment to help with identifying Entities
     *
     * @param bool $created
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function initDebugIds(bool $created = false)
    {
        $this->debugIdAsString = (string)$this->id;
        $this->debugObjectHash = spl_object_hash($this);
        if (false === $created) {
            return;
        }
        static $increment = 0;
        $this->debugCreationIncrement = ++$increment;
    }
}