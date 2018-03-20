<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategories;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoriesAbstract;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

/**
 * Trait HasCustomerCategoriesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to CustomerCategory.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerCategory\HasCustomerCategories
 */
trait HasCustomerCategoriesUnidirectionalOneToMany
{
    use HasCustomerCategoriesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerCategories(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            CustomerCategory::getPlural(),
            CustomerCategory::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.CustomerCategory::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            CustomerCategory::getSingular().'_'.CustomerCategory::getIdField(),
            CustomerCategory::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
