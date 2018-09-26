<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories;

use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidFactory
{

    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    private $orderedTimeFactory;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getOrderedTimeUuid(): UuidInterface
    {
        return $this->getOrderedTimeFactory()->uuid1();
    }

    private function getOrderedTimeFactory(): \Ramsey\Uuid\UuidFactory
    {
        if (null !== $this->orderedTimeFactory) {
            return $this->orderedTimeFactory;
        }
        $this->orderedTimeFactory = clone $this->uuid::getFactory();
        $codec                    = new OrderedTimeCodec(
            $this->orderedTimeFactory->getUuidBuilder()
        );
        $this->orderedTimeFactory->setCodec($codec);

        return $this->orderedTimeFactory;
    }

    public function getUuid()
    {
        return $this->uuid::uuid4();
    }
}