<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomersAbstract;

/**
 * Trait HasCustomersUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Customer.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Customer\HasCustomers
 */
trait HasCustomersUnidirectionalOneToMany
{
    use HasCustomersAbstract;

    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Customer::getPlural(),
            Customer::class
        );
    }
}
