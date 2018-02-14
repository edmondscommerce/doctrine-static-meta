<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

interface HasCustomers
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForCustomers(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection;

    /**
     * @param Collection|Customer[] $customers
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setCustomers(Collection $customers): UsesPHPMetaDataInterface;

    /**
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

}
