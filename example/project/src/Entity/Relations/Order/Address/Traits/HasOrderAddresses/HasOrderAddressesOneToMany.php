<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressesAbstract;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;
use My\Test\Project\Entities\Order\Address as OrderAddress;

/**
 * Trait HasOrderAddressesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to OrderAddress.
 *
 * The OrderAddress has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderAddress\HasOrderAddresses
 */
trait HasOrderAddressesOneToMany
{
    use HasOrderAddressesAbstract;

    use ReciprocatesOrderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderAddresses(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            OrderAddress::getPlural(),
            OrderAddress::class,
            static::getSingular()
        );
    }
}
