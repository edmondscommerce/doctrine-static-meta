<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Traits\HasCustomerAbstract;
use My\Test\Project\Entities\Customer;

trait HasCustomerUnidirectionalOneToOne
{
    use HasCustomerAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomer(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
