<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;

trait HasCustomerUnidirectionalOneToOne
{
    use HasCustomerAbstract;

    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
