<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategoryAbstract;

/**
 * Trait HasCategoryManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategory
 */
trait HasCategoryUnidirectionalManyToOne
{
    use HasCategoryAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Category::getSingular(),
            Category::class
        );
    }
}
