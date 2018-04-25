<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address as OrderAddress;

interface ReciprocatesOrderAddressInterface
{
    /**
     * @param OrderAddress $orderAddress
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): UsesPHPMetaDataInterface;

    /**
     * @param OrderAddress $orderAddress
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): UsesPHPMetaDataInterface;
}
