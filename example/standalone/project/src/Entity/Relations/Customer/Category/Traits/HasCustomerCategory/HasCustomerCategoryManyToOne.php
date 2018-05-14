<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCustomerCategory;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoryAbstract;

/**
 * Trait HasCustomerCategoryManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of CustomerCategory.
 *
 * CustomerCategory has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerCategory\HasCustomerCategory
 */
trait HasCustomerCategoryManyToOne
{
    use HasCustomerCategoryAbstract;

    use ReciprocatesCustomerCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerCategory(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            CustomerCategory::getSingular(),
            CustomerCategory::class,
            static::getPlural()
        );
    }
}
