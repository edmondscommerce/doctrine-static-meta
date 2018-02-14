<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategories;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategoriesAbstract;
use  My\Test\Project\EntityRelations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;

trait HasCategoriesInverseManyToMany
{
    use HasCategoriesAbstract;

    use ReciprocatesCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Category::getPlural(), Category::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Category::getPlural().'_to_'.static::getPlural());
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
