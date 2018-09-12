<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationAbstract;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\ReciprocatesLargeRelation;

/**
 * Trait HasLargeRelationManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of LargeRelation.
 *
 * LargeRelation has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeRelation\HasLargeRelation
 */
// phpcs:enable
trait HasLargeRelationManyToOne
{
    use HasLargeRelationAbstract;

    use ReciprocatesLargeRelation;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeRelation(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            LargeRelation::getDoctrineStaticMeta()->getSingular(),
            LargeRelation::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
