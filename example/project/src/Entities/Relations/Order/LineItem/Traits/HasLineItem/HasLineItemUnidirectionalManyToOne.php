<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemAbstract;
use My\Test\Project\Entities\Order\LineItem;

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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            LineItem::getSingular(),
            LineItem::class
        );
    }
}
