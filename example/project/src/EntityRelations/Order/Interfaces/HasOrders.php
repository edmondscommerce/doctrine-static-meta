<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

interface HasOrders
{
    public static function getPropertyMetaForOrders(ClassMetadataBuilder $builder);

    public function getOrders(): Collection;

    public function setOrders(Collection $orders): UsesPHPMetaDataInterface;

    public function addOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface;

}
