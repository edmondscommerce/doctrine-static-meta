<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelations;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationsAbstract;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\ReciprocatesLargeRelation;

/**
 * Trait HasLargeRelationsOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and LargeRelation
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits\HasLargeRelations
 */
// phpcs:enable
trait HasLargeRelationsOwningManyToMany
{
    use HasLargeRelationsAbstract;

    use ReciprocatesLargeRelation;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeRelations(
        ClassMetadataBuilder $builder
    ): void {

        $manyToManyBuilder = $builder->createManyToMany(
            LargeRelation::getDoctrineStaticMeta()->getPlural(),
            LargeRelation::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(LargeRelation::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                LargeRelation::getDoctrineStaticMeta()->getSingular() . '_' . LargeRelation::PROP_ID
            ),
            LargeRelation::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
