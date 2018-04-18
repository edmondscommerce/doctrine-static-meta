<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Traits\HasCustomersAbstract;
use  My\Test\Project\Entity\Relations\Customer\Traits\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer as Customer;

trait HasCustomersOwningManyToMany
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
        $manyToManyBuilder->inversedBy(static::getPlural());
        $fromTableName = Inflector::tableize(static::getPlural());
        $toTableName   = Inflector::tableize(Customer::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
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
