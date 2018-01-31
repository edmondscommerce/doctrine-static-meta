<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

interface HasCustomer
{
    static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder);

    public function getCustomer(): ?Customer;

    public function setCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeCustomer(): UsesPHPMetaDataInterface;

}
