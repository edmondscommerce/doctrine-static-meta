<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategories;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategoriesAbstract;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;

trait HasCategoriesOwningManyToMany
{
    use HasCategoriesAbstract;

    use ReciprocatesCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder): void
    {

        $manyToManyBuilder = $builder->createManyToMany(
            Category::getPlural(), Category::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.Category::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Category::getSingular().'_'.Category::getIdField(),
            Category::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
