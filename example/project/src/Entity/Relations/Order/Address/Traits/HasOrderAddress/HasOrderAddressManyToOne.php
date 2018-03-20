<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressAbstract;

/**
 * Trait HasOrderAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of OrderAddress.
 *
 * OrderAddress has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderAddress\HasOrderAddress
 */
trait HasOrderAddressManyToOne
{
    use HasOrderAddressAbstract;

    use ReciprocatesOrderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            OrderAddress::getSingular(),
            OrderAddress::class,
            static::getPlural()
        );
    }
}
