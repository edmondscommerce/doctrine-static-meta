<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface ReciprocatesLineItem
{
    /**
     * @param LineItem $lineItem
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface;

    /**
     * @param LineItem $lineItem
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface;
}
