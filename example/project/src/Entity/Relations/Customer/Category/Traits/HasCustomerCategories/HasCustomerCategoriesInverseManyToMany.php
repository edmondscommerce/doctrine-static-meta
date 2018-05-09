<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategories;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoriesAbstract;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCustomerCategory;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

trait HasCustomerCategoriesInverseManyToMany
{
    use HasCustomerCategoriesAbstract;

    use ReciprocatesCustomerCategory;

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
        $manyToManyBuilder->mappedBy(static::getPlural());
        $fromTableName = Inflector::tableize(CustomerCategory::getPlural());
        $toTableName   = Inflector::tableize(static::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
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
