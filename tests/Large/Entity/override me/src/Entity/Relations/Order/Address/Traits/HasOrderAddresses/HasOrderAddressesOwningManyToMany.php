<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressesAbstract;
use My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;

/**
 * Trait HasOrderAddressesOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and OrderAddress
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits\HasOrderAddresses
 */
// phpcs:enable
trait HasOrderAddressesOwningManyToMany
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

        $manyToManyBuilder = $builder->createManyToMany(
            OrderAddress::getDoctrineStaticMeta()->getPlural(),
            OrderAddress::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(OrderAddress::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                OrderAddress::getDoctrineStaticMeta()->getSingular() . '_' . OrderAddress::PROP_ID
            ),
            OrderAddress::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
