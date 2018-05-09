<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;

trait ReciprocatesCustomerCategory
{
    /**
     * This method needs to set the relationship on the customerCategory to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param CustomerCategory $customerCategory
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): UsesPHPMetaDataInterface {
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
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCustomerCategory(
        CustomerCategory $customerCategory
    ): UsesPHPMetaDataInterface {
        $method = 'remove'.static::getSingular();
        $customerCategory->$method($this, false);

        return $this;
    }
}
