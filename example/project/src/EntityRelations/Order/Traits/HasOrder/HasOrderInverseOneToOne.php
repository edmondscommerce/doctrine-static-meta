<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Order\Traits\HasOrder;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\EntityRelations\Order\Traits\HasOrderAbstract;

trait HasOrderInverseOneToOne
{
    use HasOrderAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            Order::getSingular(),
            Order::class,
            static::getSingular()
        );
    }
}
