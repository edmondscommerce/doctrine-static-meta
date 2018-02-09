<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

trait ReciprocatesLineItem
{
    /**
     * This method needs to set the relationship on the lineItem to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param LineItem $lineItem
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($lineItem, $method)) {
            $method = 'set'.$singular;
        }

        $lineItem->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the lineItem to this entity.
     *
     * @param LineItem $lineItem
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $lineItem->$method($this, false);

        return $this;
    }

}
