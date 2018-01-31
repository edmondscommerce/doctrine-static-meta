<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategories;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategoriesAbstract;

/**
 * Trait HasCategoriesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Category.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategories
 */
trait HasCategoriesUnidirectionalOneToMany
{
    use HasCategoriesAbstract;

    public static function getPropertyMetaForCategory(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Category::getPlural(),
            Category::class
        );
    }
}
