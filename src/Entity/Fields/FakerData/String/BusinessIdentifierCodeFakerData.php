<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;

class BusinessIdentifierCodeFakerData extends AbstractFakerDataProvider
{

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }


    public function __invoke(): string
    {
        return $this->generator->swiftBicNumber;
    }
}
