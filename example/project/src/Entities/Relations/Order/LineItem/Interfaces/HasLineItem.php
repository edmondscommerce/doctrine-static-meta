<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface HasLineItem
{
    static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder);

    public function getLineItem(): ?LineItem;

    public function setLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeLineItem(): UsesPHPMetaDataInterface;

}
