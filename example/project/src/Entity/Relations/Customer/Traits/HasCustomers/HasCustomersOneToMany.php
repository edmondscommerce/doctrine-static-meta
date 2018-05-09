<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomersAbstract;
use My\Test\Project\Entity\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer as Customer;

/**
 * Trait HasCustomersOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Customer.
 *
 * The Customer has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Customer\HasCustomers
 */
trait HasCustomersOneToMany
{
    use HasCustomersAbstract;

    use ReciprocatesCustomer;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomers(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Customer::getPlural(),
            Customer::class,
            static::getSingular()
        );
    }
}
