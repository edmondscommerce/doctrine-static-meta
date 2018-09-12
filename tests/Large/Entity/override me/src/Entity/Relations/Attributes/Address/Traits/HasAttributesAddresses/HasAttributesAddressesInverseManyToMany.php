<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddresses;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressesAbstract;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\ReciprocatesAttributesAddress;

/**
 * Trait HasAttributesAddressesInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And AttributesAddress
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits\HasAttributesAddresses
 */
// phpcs:enable
trait HasAttributesAddressesInverseManyToMany
{
    use HasAttributesAddressesAbstract;

    use ReciprocatesAttributesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesAddresses(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            AttributesAddress::getDoctrineStaticMeta()->getPlural(),
            AttributesAddress::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(AttributesAddress::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                AttributesAddress::getDoctrineStaticMeta()->getSingular() . '_' . AttributesAddress::PROP_ID
            ),
            AttributesAddress::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
