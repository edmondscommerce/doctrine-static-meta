<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

trait ReciprocatesCategory
{
    /**
     * This method needs to set the relationship on the category to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Category $category
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCategory(Category $category): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($category, $method)) {
            $method = 'set'.$singular;
        }

        $category->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the category to this entity.
     *
     * @param Category $category
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCategory(Category $category): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $category->$method($this, false);

        return $this;
    }
}