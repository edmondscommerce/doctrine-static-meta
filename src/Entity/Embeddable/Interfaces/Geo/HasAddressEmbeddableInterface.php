<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\HasEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;

interface HasAddressEmbeddableInterface extends HasEmbeddableInterface
{
    public const PROP_ADDRESS_EMBEDDABLE = 'addressEmbeddable';
    public const COLUMN_PREFIX_ADDRESS   = 'address_';

    public function getAddressEmbeddable(): AddressEmbeddableInterface;

    public function setAddressEmbeddable(AddressEmbeddableInterface $embeddable);
}
