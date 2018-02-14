<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

interface HasLineItem
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder): void;

    /**
     * @return null|LineItem
     */
    public function getLineItem(): ?LineItem;

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeLineItem(): UsesPHPMetaDataInterface;
}
