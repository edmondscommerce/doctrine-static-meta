<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressesAbstract;
use My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;

/**
 * Trait HasOrderAddressesOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to OrderAddress.
 *
 * The OrderAddress has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\OrderAddress\HasOrderAddresses
 */
// phpcs:enable
trait HasOrderAddressesOneToMany
{
    use HasOrderAddressesAbstract;

    use ReciprocatesOrderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForOrderAddresses(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            OrderAddress::getDoctrineStaticMeta()->getPlural(),
            OrderAddress::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
