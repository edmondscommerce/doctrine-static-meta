<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

interface ReciprocatesOrder
{
    /**
     * @param Order $order
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnOrder(Order $order): UsesPHPMetaDataInterface;

    /**
     * @param Order $order
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnOrder(Order $order): UsesPHPMetaDataInterface;
}
