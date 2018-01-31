<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemAbstract;

trait HasLineItemUnidirectionalOneToOne
{
    use HasLineItemAbstract;

    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            LineItem::getSingular(),
            LineItem::class
        );
    }
}
