<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelations;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationsAbstract;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\ReciprocatesLargeRelation;

/**
 * Trait HasLargeRelationsOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to LargeRelation.
 *
 * The LargeRelation has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeRelation\HasLargeRelations
 */
// phpcs:enable
trait HasLargeRelationsOneToMany
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
        $builder->addOneToMany(
            LargeRelation::getDoctrineStaticMeta()->getPlural(),
            LargeRelation::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
