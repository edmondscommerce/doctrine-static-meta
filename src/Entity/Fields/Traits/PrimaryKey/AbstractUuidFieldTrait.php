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
    private $objectId;

    /**
     * The UUID as a string, only for debugging purposes
     *
     * @var string
     */
    private $idAsString;

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
        $this->id         = $uuid;
        $this->idAsString = $this->id->__toString();
        $this->objectId   = spl_object_hash($this);

        return $this;
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
