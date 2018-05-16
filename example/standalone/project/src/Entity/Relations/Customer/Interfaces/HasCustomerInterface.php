<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer as Customer;

interface HasCustomerInterface
{
    public const PROPERTY_NAME_CUSTOMER = 'customer';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForCustomer(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Customer
     */
    public function getCustomer(): ?Customer;

    /**
     * @param Customer|null $customer
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomer(
        ?Customer $customer,
        bool $recip = true
    ): HasCustomerInterface;

    /**
     * @return self
     */
    public function removeCustomer(): HasCustomerInterface;
}