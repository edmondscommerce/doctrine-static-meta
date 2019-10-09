<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface HasAddressEmbeddableInterface extends EntityInterface
{
    public const PROP_ADDRESS_EMBEDDABLE = 'addressEmbeddable';
    public const COLUMN_PREFIX_ADDRESS   = 'address_';

    public function getAddressEmbeddable(): AddressEmbeddableInterface;
}
