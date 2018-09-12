<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;

interface HasAttributesAddressInterface
{
    public const PROPERTY_NAME_ATTRIBUTES_ADDRESS = 'attributesAddress';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAttributesAddress(ClassMetadataBuilder $builder): void;

    /**
     * @return null|AddressInterface
     */
    public function getAttributesAddress(): ?AddressInterface;

    /**
     * @param AddressInterface|null $attributesAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAttributesAddress(
        ?AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressInterface;

    /**
     * @param null|AddressInterface $attributesAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesAddress(
        ?AddressInterface $attributesAddress = null,
        bool $recip = true
    ): HasAttributesAddressInterface;
}
