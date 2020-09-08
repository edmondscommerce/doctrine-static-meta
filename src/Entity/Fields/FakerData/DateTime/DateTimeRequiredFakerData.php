<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\UuidFactory;

class DateTimeRequiredFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return \DateTimeImmutable::createFromMutable($this->generator->dateTime);
    }
}
