<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Interfaces;

use My\Test\Project\Entities\Customer\Category as CustomerCategory;

interface ReciprocatesCustomerCategoryInterface
{
    /**
     * @param CustomerCategory $customerCategory
     *
     * @return self
     */
    public function reciprocateRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): self;

    /**
     * @param CustomerCategory $customerCategory
     *
     * @return self
     */
    public function removeRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): self;
}
