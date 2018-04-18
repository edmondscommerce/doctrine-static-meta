<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategories;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoriesAbstract;
use  My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCustomerCategory;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

/**
 * Trait HasCustomerCategoriesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to CustomerCategory.
 *
 * The CustomerCategory has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerCategory\HasCustomerCategories
 */
trait HasCustomerCategoriesOneToMany
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
        $builder->addOneToMany(
            CustomerCategory::getPlural(),
            CustomerCategory::class,
            static::getSingular()
        );
    }
}
