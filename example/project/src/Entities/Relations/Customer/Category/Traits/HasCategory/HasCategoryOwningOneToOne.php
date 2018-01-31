<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategory;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategoryAbstract;

trait HasCategoryOwningOneToOne
{
    use HasCategoryAbstract;

    use ReciprocatesCategory;

    public static function getPropertyMetaForCategory(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Category::getSingular(),
            Category::class,
            static::getSingular()
        );
    }
}
