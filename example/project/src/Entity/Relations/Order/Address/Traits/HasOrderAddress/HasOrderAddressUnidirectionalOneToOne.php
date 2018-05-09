<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressAbstract;
use My\Test\Project\Entities\Order\Address as OrderAddress;

trait HasOrderAddressUnidirectionalOneToOne
{
    use HasOrderAddressAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            OrderAddress::getSingular(),
            OrderAddress::class
        );
    }
}
