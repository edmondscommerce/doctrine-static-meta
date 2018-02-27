<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItemAbstract;

trait HasLineItemInverseOneToOne
{
    use HasLineItemAbstract;

    use ReciprocatesLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            LineItem::getSingular(),
            LineItem::class,
            static::getSingular()
        );
    }
}