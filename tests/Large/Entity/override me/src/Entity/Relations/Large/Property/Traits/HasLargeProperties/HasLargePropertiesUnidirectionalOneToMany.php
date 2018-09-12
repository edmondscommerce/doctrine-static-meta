<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperties;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertiesAbstract;

/**
 * Trait HasLargePropertiesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to LargeProperty.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeProperty\HasLargeProperties
 */
// phpcs:enable
trait HasLargePropertiesUnidirectionalOneToMany
{
    use HasLargePropertiesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeProperties(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            LargeProperty::getDoctrineStaticMeta()->getPlural(),
            LargeProperty::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(LargeProperty::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular().'_'.static::PROP_ID,
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            LargeProperty::getDoctrineStaticMeta()->getSingular().'_'.LargeProperty::PROP_ID,
            LargeProperty::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
