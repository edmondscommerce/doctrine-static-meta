<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Address\Traits\HasAddressAbstract;
use My\Test\Project\Entities\Address as Address;

/**
 * Trait HasAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddress
 */
trait HasAddressUnidirectionalManyToOne
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
        $builder->addManyToOne(
            Address::getSingular(),
            Address::class
        );
    }
}
