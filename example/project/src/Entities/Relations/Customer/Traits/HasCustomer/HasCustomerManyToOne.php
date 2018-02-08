<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;

/**
 * Trait HasCustomerManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Customer.
 *
 * Customer has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Customer\HasCustomer
 */
trait HasCustomerManyToOne
{
    use HasCustomerAbstract;

    use ReciprocatesCustomer;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Customer::getSingular(),
            Customer::class,
            static::getPlural()
        );
    }
}
