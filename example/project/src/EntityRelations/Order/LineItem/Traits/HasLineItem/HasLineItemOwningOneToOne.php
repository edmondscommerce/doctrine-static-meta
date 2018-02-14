<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItemAbstract;

trait HasLineItemOwningOneToOne
{
    use HasLineItemAbstract;

    use ReciprocatesLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            LineItem::getSingular(),
            LineItem::class,
            static::getSingular()
        );
    }
}
