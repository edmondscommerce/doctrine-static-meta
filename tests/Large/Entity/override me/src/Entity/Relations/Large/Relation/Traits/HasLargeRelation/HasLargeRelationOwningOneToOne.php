<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationAbstract;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\ReciprocatesLargeRelation;

/**
 * Trait HasLargeRelationOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and LargeRelation
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits\HasLargeRelation
 */
// phpcs:enable
trait HasLargeRelationOwningOneToOne
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
        $builder->addOwningOneToOne(
            LargeRelation::getDoctrineStaticMeta()->getSingular(),
            LargeRelation::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
