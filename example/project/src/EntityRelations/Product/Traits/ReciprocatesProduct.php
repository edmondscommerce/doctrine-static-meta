<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

trait ReciprocatesProduct
{
    /**
     * This method needs to set the relationship on the product to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Product $product
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnProduct(Product $product): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($product, $method)) {
            $method = 'set'.$singular;
        }

        $product->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the product to this entity.
     *
     * @param Product $product
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnProduct(Product $product): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $product->$method($this, false);

        return $this;
    }
}
