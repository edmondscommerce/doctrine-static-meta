<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategories;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategoriesAbstract;

trait HasCategoriesInverseManyToMany
{
    use HasCategoriesAbstract;

    use ReciprocatesCategory;

    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Category::getPlural(), Category::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Category::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Category::getSingular() . '_' . Category::getIdField(),
            Category::getIdField()
        );
        $builder->build();
    }
}
