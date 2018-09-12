<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelations;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationsAbstract;

/**
 * Trait HasLargeRelationsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to LargeRelation.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeRelation\HasLargeRelations
 */
// phpcs:enable
trait HasLargeRelationsUnidirectionalOneToMany
{
    use HasLargeRelationsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeRelations(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            LargeRelation::getDoctrineStaticMeta()->getPlural(),
            LargeRelation::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(LargeRelation::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular().'_'.static::PROP_ID,
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            LargeRelation::getDoctrineStaticMeta()->getSingular().'_'.LargeRelation::PROP_ID,
            LargeRelation::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
