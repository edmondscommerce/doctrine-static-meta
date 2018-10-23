<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use Ramsey\Uuid\UuidInterface;

trait AbstractUuidFieldTrait
{
    /**
     * @var UuidInterface
     */
    private $id;

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

    abstract public static function buildUuid(UuidFactory $uuidFactory): UuidInterface;

    /**
     * This is leveraging the setter injection that happens on Entity creation to ensure that the UUID is set
     *
     * @param UuidFactory $uuidFactory
     */
    public function injectUuid(UuidFactory $uuidFactory)
    {
        if (null === $this->id) {
            $this->setId(self::buildUuid($uuidFactory));
        }
    }

    private function setId(?UuidInterface $uuid): self
    {
        $this->id = $uuid;
        $this->initDebugIds(true);

        return $this;
    }

    /**
     * When creating a new Entity, we track the increment to help with identifying Entities
     *
     * @param bool $created
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

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->id;
    }
}
