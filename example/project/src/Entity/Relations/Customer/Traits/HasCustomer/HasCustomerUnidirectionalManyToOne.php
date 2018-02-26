<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomer;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Traits\HasCustomerAbstract;
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomer(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Customer::getSingular(),
            Customer::class
        );
    }
}
