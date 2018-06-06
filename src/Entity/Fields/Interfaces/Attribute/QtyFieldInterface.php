<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface QtyFieldInterface
{
    public const PROP_QTY = 'qty';

    public const DEFAULT_QTY = 0;

    public function getQty(): int;

    public function setQty(int $qty);
}
