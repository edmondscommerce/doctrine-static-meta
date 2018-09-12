<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;

interface HasAttributesAddressesInterface
{
    public const PROPERTY_NAME_ATTRIBUTES_ADDRESSES = 'attributesAddresses';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAttributesAddresses(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|AddressInterface[]
     */
    public function getAttributesAddresses(): Collection;

    /**
     * @param Collection|AddressInterface[] $attributesAddresses
     *
     * @return self
     */
    public function setAttributesAddresses(Collection $attributesAddresses): self;

    /**
     * @param AddressInterface|null $attributesAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAttributesAddress(
        ?AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressesInterface;

    /**
     * @param AddressInterface $attributesAddress
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesAddress(
        AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressesInterface;

}
