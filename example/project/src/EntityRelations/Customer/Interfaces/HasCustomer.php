<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

interface HasCustomer
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder): void;

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
