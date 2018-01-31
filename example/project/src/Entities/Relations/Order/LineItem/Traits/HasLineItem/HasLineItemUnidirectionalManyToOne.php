<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemAbstract;

/**
 * Trait HasLineItemManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\LineItem\HasLineItem
 */
trait HasLineItemUnidirectionalManyToOne
{
    use HasLineItemAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            LineItem::getSingular(),
            LineItem::class
        );
    }
}
