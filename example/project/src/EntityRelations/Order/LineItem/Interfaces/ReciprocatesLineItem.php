<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface ReciprocatesLineItem
{
    public function reciprocateRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface;

    public function removeRelationOnLineItem(LineItem $lineItem): UsesPHPMetaDataInterface;
}
