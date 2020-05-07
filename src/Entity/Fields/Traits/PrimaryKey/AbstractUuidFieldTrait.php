<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Debug\DebugEntityDataObjectIds;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;

trait AbstractUuidFieldTrait
{
    /**
     * @var UuidInterface
     */
    private UuidInterface $id;

    use DebugEntityDataObjectIds;

    abstract public static function buildUuid(UuidFactory $uuidFactory): UuidInterface;

    /**
     * This is leveraging the setter injection that happens on Entity creation to ensure that the UUID is set
     *
     * @param UuidFactory $uuidFactory
     */
    public function injectUuid(UuidFactory $uuidFactory): void
    {
        if (null === $this->id) {
            $this->setId(static::buildUuid($uuidFactory));
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->id;
    }

    private function setId(UuidInterface $uuid): self
    {
        if (null !== $this->id) {
            throw new InvalidArgumentException('Trying to update ID when it has already been set');
        }
        $this->id = $uuid;
        $this->initDebugIds(true);

        return $this;
    }
}
