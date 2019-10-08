<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Binary;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\UuidFactory;

class BinaryUuidFakerData extends AbstractFakerDataProvider
{
    /**
     * @var UuidFactory
     */
    private $factory;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->factory = $this->createUuidFactory();
    }

    private function createUuidFactory(): UuidFactory
    {
        $factory = new UuidFactory();
        $codec   = new OrderedTimeCodec(
            $factory->getUuidBuilder()
        );
        $factory->setCodec($codec);

        return $factory;
    }

    public function __invoke()
    {
        return $this->factory->uuid1();
    }
}
