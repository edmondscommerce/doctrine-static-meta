<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

interface HasCustomerCategoriesInterface
{
    public const PROPERTY_NAME_CUSTOMER_CATEGORIES = 'customerCategories';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForCustomerCategories(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|CustomerCategory[]
     */
    public function getCustomerCategories(): Collection;

    /**
     * @param Collection|CustomerCategory[] $customerCategories
     *
     * @return self
     */
    public function setCustomerCategories(Collection $customerCategories): self;

    /**
     * @param CustomerCategory|null $customerCategory
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomerCategory(
        ?CustomerCategory $customerCategory,
        bool $recip = true
    ): HasCustomerCategoriesInterface;

    /**
     * @param CustomerCategory $customerCategory
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomerCategory(
        CustomerCategory $customerCategory,
        bool $recip = true
    ): HasCustomerCategoriesInterface;
}
