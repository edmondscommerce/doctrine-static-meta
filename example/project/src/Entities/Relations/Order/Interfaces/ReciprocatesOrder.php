<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

interface ReciprocatesOrder
{
    public function reciprocateRelationOnOrder(Order $order): UsesPHPMetaDataInterface;

    public function removeRelationOnOrder(Order $order): UsesPHPMetaDataInterface;
}
