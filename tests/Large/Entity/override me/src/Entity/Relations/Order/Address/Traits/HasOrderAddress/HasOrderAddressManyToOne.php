<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressAbstract;
use My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;

/**
 * Trait HasOrderAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of OrderAddress.
 *
 * OrderAddress has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\OrderAddress\HasOrderAddress
 */
// phpcs:enable
trait HasOrderAddressManyToOne
{
    use HasOrderAddressAbstract;

    use ReciprocatesOrderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForOrderAddress(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            OrderAddress::getDoctrineStaticMeta()->getSingular(),
            OrderAddress::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
