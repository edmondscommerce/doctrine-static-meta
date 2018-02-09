<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemsAbstract;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;

trait HasLineItemsOwningManyToMany
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
            LineItem::getPlural(), LineItem::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.LineItem::getPlural());
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
