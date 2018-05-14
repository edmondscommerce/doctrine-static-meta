<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Traits;

use My\Test\Project\Entities\Product as Product;
use My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;

trait ReciprocatesProduct
{
    /**
     * This method needs to set the relationship on the product to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Product|null $product
     *
     * @return ReciprocatesProductInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnProduct(
        Product $product
    ): ReciprocatesProductInterface {
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
     * @return ReciprocatesProductInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnProduct(
        Product $product
    ): ReciprocatesProductInterface {
        $method = 'remove'.static::getSingular();
        $product->$method($this, false);

        return $this;
    }
}
