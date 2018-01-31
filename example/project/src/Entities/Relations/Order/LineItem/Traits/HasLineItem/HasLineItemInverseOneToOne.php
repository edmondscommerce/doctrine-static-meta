<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemAbstract;

trait HasLineItemInverseOneToOne
{
    use HasLineItemAbstract;

    use ReciprocatesLineItem;

    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            LineItem::getSingular(),
            LineItem::class,
            static::getSingular()
        );
    }
}
