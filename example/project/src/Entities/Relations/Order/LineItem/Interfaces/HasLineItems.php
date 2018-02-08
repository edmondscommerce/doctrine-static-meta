<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface HasLineItems
{
    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder);

    public function getLineItems(): Collection;

    public function setLineItems(Collection $lineItems): UsesPHPMetaDataInterface;

    public function addLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;

}
