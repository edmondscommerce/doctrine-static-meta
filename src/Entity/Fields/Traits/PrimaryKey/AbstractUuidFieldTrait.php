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
     * This is leveraging the setter injection that happens on Entity creation to ensure that the UUID is set
     *
     * @param UuidFactory $uuidFactory
     */
    public function injectUuid(UuidFactory $uuidFactory)
    {
        if (null === $this->id) {
            $this->setUuid($uuidFactory);
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    abstract protected function setUuid(UuidFactory $uuidFactory);
}
