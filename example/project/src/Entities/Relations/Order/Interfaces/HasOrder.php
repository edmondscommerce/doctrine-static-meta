<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

interface HasOrder
{
    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder);

    public function getOrder(): ?Order;

    public function setOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeOrder(): UsesPHPMetaDataInterface;

}
