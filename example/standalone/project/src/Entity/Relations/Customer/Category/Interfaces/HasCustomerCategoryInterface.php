<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

interface HasCustomerCategoryInterface
{
    public const PROPERTY_NAME_CUSTOMER_CATEGORY = 'customerCategory';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForCustomerCategory(ClassMetadataBuilder $builder): void;

    /**
     * @return null|CustomerCategory
     */
    public function getCustomerCategory(): ?CustomerCategory;

    /**
     * @param CustomerCategory|null $customerCategory
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomerCategory(
        ?CustomerCategory $customerCategory,
        bool $recip = true
    ): HasCustomerCategoryInterface;

    /**
     * @return self
     */
    public function removeCustomerCategory(): HasCustomerCategoryInterface;
}