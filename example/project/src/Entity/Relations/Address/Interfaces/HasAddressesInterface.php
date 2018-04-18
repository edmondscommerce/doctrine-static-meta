<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Address as Address;

interface HasAddressesInterface
{
    public const PROPERTY_NAME_ADDRESSES = 'addresses';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForAddresses(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection;

    /**
     * @param Collection|Address[] $addresses
     *
     * @return self
     */
    public function setAddresses(Collection $addresses);

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAddress(
        Address $address,
        bool $recip = true
    );

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAddress(
        Address $address,
        bool $recip = true
    );
}
