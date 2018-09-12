<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddresses;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressesAbstract;

/**
 * Trait HasAttributesAddressesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to AttributesAddress.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesAddress\HasAttributesAddresses
 */
// phpcs:enable
trait HasAttributesAddressesUnidirectionalOneToMany
{
    use HasAttributesAddressesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesAddresses(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            AttributesAddress::getDoctrineStaticMeta()->getPlural(),
            AttributesAddress::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(AttributesAddress::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular().'_'.static::PROP_ID,
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            AttributesAddress::getDoctrineStaticMeta()->getSingular().'_'.AttributesAddress::PROP_ID,
            AttributesAddress::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
