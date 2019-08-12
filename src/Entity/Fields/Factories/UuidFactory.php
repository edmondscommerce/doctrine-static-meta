<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories;

use Exception;
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
