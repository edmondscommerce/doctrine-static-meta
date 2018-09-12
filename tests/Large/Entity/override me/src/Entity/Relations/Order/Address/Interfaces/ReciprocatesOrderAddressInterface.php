<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use My\Test\Project\Entity\Interfaces\Order\AddressInterface;

interface ReciprocatesOrderAddressInterface
{
    /**
     * @param AddressInterface $orderAddress
     *
     * @return self
     */
    public function reciprocateRelationOnOrderAddress(
        AddressInterface $orderAddress
    ): self;

    /**
     * @param AddressInterface $orderAddress
     *
     * @return self
     */
    public function removeRelationOnOrderAddress(
        AddressInterface $orderAddress
    ): self;
}
