<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

interface ReciprocatesAddressInterface
{
    /**
     * @param Address $address
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnAddress(Address $address): UsesPHPMetaDataInterface;

    /**
     * @param Address $address
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnAddress(Address $address): UsesPHPMetaDataInterface;
}