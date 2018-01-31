<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;

/**
 * Trait HasCustomerManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Customer\HasCustomer
 */
trait HasCustomerUnidirectionalManyToOne
{
    use HasCustomerAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForCustomers(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
