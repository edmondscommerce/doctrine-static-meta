<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\FakerData\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class AddressEmbeddableFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        $addressEmbeddable = new AddressEmbeddable();
        $addressEmbeddable->setCity($this->generator->city);
        $addressEmbeddable->setCountryCode($this->generator->countryCode);
        $addressEmbeddable->setHouseName($this->generator->name);
        $addressEmbeddable->setHouseNumber((string)$this->generator->numberBetween(1, 1000));
        $addressEmbeddable->setPostalArea($this->generator->city);
        $addressEmbeddable->setStreet($this->generator->streetName);
        $addressEmbeddable->setPostalCode($this->generator->postcode);

        return $addressEmbeddable;
    }
}
