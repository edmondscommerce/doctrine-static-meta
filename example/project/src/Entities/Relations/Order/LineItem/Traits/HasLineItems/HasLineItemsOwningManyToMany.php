<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemsAbstract;

trait HasLineItemsOwningManyToMany
{
    use HasLineItemsAbstract;

    use ReciprocatesLineItem;

    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            LineItem::getPlural(), LineItem::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . LineItem::getPlural());
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
