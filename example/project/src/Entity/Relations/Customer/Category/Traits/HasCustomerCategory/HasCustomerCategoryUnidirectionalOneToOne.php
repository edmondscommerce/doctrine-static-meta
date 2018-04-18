<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategoryAbstract;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

trait HasCustomerCategoryUnidirectionalOneToOne
{
    use HasCustomerCategoryAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerCategory(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            CustomerCategory::getSingular(),
            CustomerCategory::class
        );
    }
}
