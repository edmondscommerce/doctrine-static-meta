<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItemsAbstract;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;

trait HasLineItemsInverseManyToMany
{
    use HasLineItemsAbstract;

    use ReciprocatesLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            LineItem::getPlural(),
            LineItem::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(LineItem::getPlural().'_to_'.static::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            LineItem::getSingular().'_'.LineItem::getIdField(),
            LineItem::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
