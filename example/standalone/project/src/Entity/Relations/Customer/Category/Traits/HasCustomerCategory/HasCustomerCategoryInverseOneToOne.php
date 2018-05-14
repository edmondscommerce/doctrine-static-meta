<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCustomerCategory;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoryAbstract;

trait HasCustomerCategoryInverseOneToOne
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
        $builder->addInverseOneToOne(
            CustomerCategory::getSingular(),
            CustomerCategory::class,
            static::getSingular()
        );
    }
}
