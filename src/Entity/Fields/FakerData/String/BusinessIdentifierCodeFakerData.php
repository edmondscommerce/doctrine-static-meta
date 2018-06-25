<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class BusinessIdentifierCodeFakerData extends AbstractFakerDataProvider
{
    public function __invoke(): string
    {
        return $this->generator->swiftBicNumber;
    }
}
