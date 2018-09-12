<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationAbstract;



/**
 * Trait HasLargeRelationManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of LargeRelation
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeRelation\HasLargeRelation
 */
// phpcs:enable
trait HasLargeRelationUnidirectionalManyToOne
{
    use HasLargeRelationAbstract;

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
            LargeRelation::class
        );
    }
}
