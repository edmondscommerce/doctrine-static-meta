<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

interface ReciprocatesCustomerCategoryInterface
{
    /**
     * @param CustomerCategory $customerCategory
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): UsesPHPMetaDataInterface;

    /**
     * @param CustomerCategory $customerCategory
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): UsesPHPMetaDataInterface;
}
