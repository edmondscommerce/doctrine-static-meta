<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface HasLineItems
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|LineItem[]
     */
    public function getLineItems(): Collection;

    /**
     * @param Collection|LineItem[] $lineItems
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setLineItems(Collection $lineItems): UsesPHPMetaDataInterface;

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;
}
