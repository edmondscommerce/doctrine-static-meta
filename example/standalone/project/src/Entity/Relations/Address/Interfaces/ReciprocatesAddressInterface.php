<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Interfaces;

use My\Test\Project\Entities\Address as Address;

interface ReciprocatesAddressInterface
{
    /**
     * @param Address $address
     *
     * @return self
     */
    public function reciprocateRelationOnAddress(
        Address $address
    ): self;

    /**
     * @param Address $address
     *
     * @return self
     */
    public function removeRelationOnAddress(
        Address $address
    ): self;
}
