<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

trait ReciprocatesBrand
{
    /**
     * This method needs to set the relationship on the brand to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Brand $brand
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($brand, $method)) {
            $method = 'set'.$singular;
        }

        $brand->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the brand to this entity.
     *
     * @param Brand $brand
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $brand->$method($this, false);

        return $this;
    }

}
