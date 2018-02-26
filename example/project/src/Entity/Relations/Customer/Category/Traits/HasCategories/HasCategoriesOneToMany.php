<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategories;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategoriesAbstract;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;

/**
 * Trait HasCategoriesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Category.
 *
 * The Category has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategories
 */
trait HasCategoriesOneToMany
{
    use HasCategoriesAbstract;

    use ReciprocatesCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCategories(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Category::getPlural(),
            Category::class,
            static::getSingular()
        );
    }
}
