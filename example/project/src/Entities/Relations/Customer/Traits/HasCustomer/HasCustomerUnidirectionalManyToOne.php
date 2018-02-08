<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomerAbstract;
use My\Test\Project\Entities\Customer;

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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
