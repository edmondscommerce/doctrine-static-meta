<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

trait ReciprocatesProductBrand
{
    /**
     * This method needs to set the relationship on the productBrand to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param ProductBrand $productBrand
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnProductBrand(
        ProductBrand $productBrand
    ): UsesPHPMetaDataInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($productBrand, $method)) {
            $method = 'set'.$singular;
        }

        $productBrand->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the productBrand to this entity.
     *
     * @param ProductBrand $productBrand
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnProductBrand(
        ProductBrand $productBrand
    ): UsesPHPMetaDataInterface {
        $method = 'remove'.static::getSingular();
        $productBrand->$method($this, false);

        return $this;
    }

}
