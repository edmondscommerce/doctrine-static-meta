<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Order\Address\Traits\HasAddressAbstract;
use My\Test\Project\Entities\Order\Address;

trait HasAddressUnidirectionalOneToOne
{
    use HasAddressAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Address::getSingular(),
            Address::class
        );
    }
}
