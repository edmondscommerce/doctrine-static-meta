<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemsAbstract;

trait HasLineItemsInverseManyToMany
{
    use HasLineItemsAbstract;

    use ReciprocatesLineItem;

    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            LineItem::getPlural(), LineItem::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(LineItem::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            LineItem::getSingular() . '_' . LineItem::getIdField(),
            LineItem::getIdField()
        );
        $builder->build();
    }
}
