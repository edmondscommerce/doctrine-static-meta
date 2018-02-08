<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

interface HasCustomers
{
    public static function getPropertyMetaForCustomers(ClassMetadataBuilder $builder);

    public function getCustomers(): Collection;

    public function setCustomers(Collection $customers): UsesPHPMetaDataInterface;

    public function addCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

}
