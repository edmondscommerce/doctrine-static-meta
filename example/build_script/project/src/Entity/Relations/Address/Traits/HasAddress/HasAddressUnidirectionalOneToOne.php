<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Address\Traits\HasAddressAbstract;
use My\Test\Project\Entities\Address as Address;

trait HasAddressUnidirectionalOneToOne
{
    use HasAddressAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Address::getSingular(),
            Address::class
        );
    }
}
