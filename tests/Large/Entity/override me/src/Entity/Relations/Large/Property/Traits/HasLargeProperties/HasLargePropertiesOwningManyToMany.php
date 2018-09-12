<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperties;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertiesAbstract;
use My\Test\Project\Entity\Relations\Large\Property\Traits\ReciprocatesLargeProperty;

/**
 * Trait HasLargePropertiesOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and LargeProperty
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\LargeProperty\Traits\HasLargeProperties
 */
// phpcs:enable
trait HasLargePropertiesOwningManyToMany
{
    use HasLargePropertiesAbstract;

    use ReciprocatesLargeProperty;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeProperties(
        ClassMetadataBuilder $builder
    ): void {

        $manyToManyBuilder = $builder->createManyToMany(
            LargeProperty::getDoctrineStaticMeta()->getPlural(),
            LargeProperty::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(LargeProperty::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                LargeProperty::getDoctrineStaticMeta()->getSingular() . '_' . LargeProperty::PROP_ID
            ),
            LargeProperty::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
