<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Traits\HasCustomers;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entities\Relations\Customer\Traits\HasCustomersAbstract;

trait HasCustomersInverseManyToMany
{
    use HasCustomersAbstract;

    use ReciprocatesCustomer;

    public static function getPropertyMetaForCustomers(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Customer::getPlural(), Customer::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Customer::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Customer::getSingular() . '_' . Customer::getIdField(),
            Customer::getIdField()
        );
        $builder->build();
    }
}
