<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;

trait HasCustomerInverseOneToOne
{
    use HasCustomerAbstract;

    use ReciprocatesCustomer;

    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            Customer::getSingular(),
            Customer::class,
            static::getSingular()
        );
    }
}
