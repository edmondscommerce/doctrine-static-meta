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

    abstract public static function buildUuid(UuidFactory $uuidFactory): UuidInterface;

    /**
     * This is leveraging the setter injection that happens on Entity creation to ensure that the UUID is set
     *
     * @param UuidFactory $uuidFactory
     */
    public function injectUuid(UuidFactory $uuidFactory)
    {
        if (null === $this->id) {
            $this->id = self::buildUuid($uuidFactory);
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    private function setId(?UuidInterface $uuid): self
    {
        $this->id = $uuid;

        return $this;
    }

    public function getUuid(): UuidInterface
    {
        return $this->id;
    }
}
