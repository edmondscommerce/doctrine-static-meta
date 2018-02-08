<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;
use My\Test\Project\Entities\Customer;

trait HasCustomerUnidirectionalOneToOne
{
    use HasCustomerAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
