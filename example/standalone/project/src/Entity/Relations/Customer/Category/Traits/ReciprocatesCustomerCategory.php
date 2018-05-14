<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use My\Test\Project\Entities\Customer\Category as CustomerCategory;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCustomerCategoryInterface;

trait ReciprocatesCustomerCategory
{
    /**
     * This method needs to set the relationship on the customerCategory to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param CustomerCategory|null $customerCategory
     *
     * @return ReciprocatesCustomerCategoryInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): ReciprocatesCustomerCategoryInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($customerCategory, $method)) {
            $method = 'set'.$singular;
        }

        $customerCategory->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the customerCategory to this entity.
     *
     * @param CustomerCategory $customerCategory
     *
     * @return ReciprocatesCustomerCategoryInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): ReciprocatesCustomerCategoryInterface {
        $method = 'remove'.static::getSingular();
        $customerCategory->$method($this, false);

        return $this;
    }
}
