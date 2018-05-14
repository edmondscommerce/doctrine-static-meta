<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use My\Test\Project\Entities\Order\Address as OrderAddress;

interface ReciprocatesOrderAddressInterface
{
    /**
     * @param OrderAddress $orderAddress
     *
     * @return self
     */
    public function reciprocateRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): self;

    /**
     * @param OrderAddress $orderAddress
     *
     * @return self
     */
    public function removeRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): self;
}
