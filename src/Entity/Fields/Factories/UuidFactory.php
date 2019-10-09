<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories;

use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\UuidInterface;

class UuidFactory
{

    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    private $orderedTimeFactory;
    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    private $uuidFactory;

    public function __construct(\Ramsey\Uuid\UuidFactory $uuidFactory)
    {

        $this->orderedTimeFactory = $this->createOrderedTimeFactory($uuidFactory);
        $this->uuidFactory        = $uuidFactory;
    }

    private function createOrderedTimeFactory(\Ramsey\Uuid\UuidFactory $uuidFactory): \Ramsey\Uuid\UuidFactory
    {
        $this->orderedTimeFactory = clone $uuidFactory;
        $codec                    = new OrderedTimeCodec(
            $this->orderedTimeFactory->getUuidBuilder()
        );
        $this->orderedTimeFactory->setCodec($codec);

        return $this->orderedTimeFactory;
    }

    /**
     * Get a UUID interface from an ordered time UUID string
     *
     * @param string $uuidString
     *
     * @return UuidInterface
     */
    public function getOrderedTimeUuidFromString(string $uuidString): UuidInterface
    {
        $uuid = $this->orderedTimeFactory->fromString($uuidString);
        if (1 !== $uuid->getVersion()) {
            throw new InvalidArgumentException(
                'UUID version is invalid, shoudl be version 1 when creating from string ' .
                $uuidString
                .
                "\n\n Make sure when querying the db you are using `bin_to_uuid(id, true)` "
                . "\ni.e passing in the second param to true to make MySQL use ordered time\n"
            );
        }

        return $uuid;
    }

    /**
     * This is used to get ordered time UUIDs as used in:
     * \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
     *
     * @return UuidInterface
     * @throws Exception
     */
    public function getOrderedTimeUuid(): UuidInterface
    {
        return $this->orderedTimeFactory->uuid1();
    }

    /**
     * This is used to generate standard UUIDs, as used in
     * \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait
     *
     * @return UuidInterface
     * @throws Exception
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuidFactory->uuid4();
    }

    /**
     * @return \Ramsey\Uuid\UuidFactory
     */
    public function getOrderedTimeFactory(): \Ramsey\Uuid\UuidFactory
    {
        return $this->orderedTimeFactory;
    }

    /**
     * @return \Ramsey\Uuid\UuidFactory
     */
    public function getUuidFactory(): \Ramsey\Uuid\UuidFactory
    {
        return $this->uuidFactory;
    }
}
