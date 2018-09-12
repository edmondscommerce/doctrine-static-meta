<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Address\Interfaces;

use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;

interface ReciprocatesAttributesAddressInterface
{
    /**
     * @param AddressInterface $attributesAddress
     *
     * @return self
     */
    public function reciprocateRelationOnAttributesAddress(
        AddressInterface $attributesAddress
    ): self;

    /**
     * @param AddressInterface $attributesAddress
     *
     * @return self
     */
    public function removeRelationOnAttributesAddress(
        AddressInterface $attributesAddress
    ): self;
}
