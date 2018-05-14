<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Address as Address;

interface HasAddressInterface
{
    public const PROPERTY_NAME_ADDRESS = 'address';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForAddress(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Address
     */
    public function getAddress(): ?Address;

    /**
     * @param Address|null $address
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAddress(
        ?Address $address,
        bool $recip = true
    ): HasAddressInterface;

    /**
     * @return self
     */
    public function removeAddress(): HasAddressInterface;
}
