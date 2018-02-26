<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

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
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeCustomer(): UsesPHPMetaDataInterface;

}
