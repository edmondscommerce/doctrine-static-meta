<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Traits\HasCustomersAbstract;
use  My\Test\Project\Entity\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;

trait HasCustomersInverseManyToMany
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
        $manyToManyBuilder = $builder->createManyToMany(
            Customer::getPlural(), Customer::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Customer::getPlural().'_to_'.static::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Customer::getSingular().'_'.Customer::getIdField(),
            Customer::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
