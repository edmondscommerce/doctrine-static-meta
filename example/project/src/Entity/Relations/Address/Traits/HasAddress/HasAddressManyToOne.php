<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address;
use  My\Test\Project\Entity\Relations\Address\Traits\HasAddressAbstract;

/**
 * Trait HasAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Address.
 *
 * Address has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddress
 */
trait HasAddressManyToOne
{
    use HasAddressAbstract;

    use ReciprocatesAddress;

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
            Address::class,
            static::getPlural()
        );
    }
}
