<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressesAbstract;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Order\Address;

/**
 * Trait HasAddressesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Address.
 *
 * The Address has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddresses
 */
trait HasAddressesOneToMany
{
    use HasAddressesAbstract;

    use ReciprocatesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Address::getPlural(),
            Address::class,
            static::getSingular()
        );
    }
}
