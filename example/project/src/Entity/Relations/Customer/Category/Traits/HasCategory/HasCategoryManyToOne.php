<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategoryAbstract;

/**
 * Trait HasCategoryManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Category.
 *
 * Category has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategory
 */
trait HasCategoryManyToOne
{
    use HasCategoryAbstract;

    use ReciprocatesCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCategory(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Category::getSingular(),
            Category::class,
            static::getPlural()
        );
    }
}
