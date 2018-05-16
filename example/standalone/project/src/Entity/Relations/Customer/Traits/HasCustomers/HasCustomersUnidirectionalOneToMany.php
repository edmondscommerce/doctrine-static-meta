<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomersAbstract;
use My\Test\Project\Entities\Customer as Customer;
use Doctrine\Common\Inflector\Inflector;

/**
 * Trait HasCustomersUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Customer.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Customer\HasCustomers
 */
trait HasCustomersUnidirectionalOneToMany
{
    use HasCustomersAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomers(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Customer::getPlural(),
            Customer::class
        );
        $fromTableName = Inflector::tableize(static::getSingular());
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