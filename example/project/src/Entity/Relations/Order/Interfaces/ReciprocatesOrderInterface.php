<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

interface ReciprocatesOrderInterface
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
