<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;

interface HasAddressEmbeddableInterface
{
    public const PROP_ADDRESS          = 'addressEmbeddable';
    public const COLUMN_PREFIX_ADDRESS = 'address_';

    public function getAddressEmbeddable(): AddressEmbeddableInterface;

    public function setAddressEmbeddable(AddressEmbeddableInterface $embeddable);
}
