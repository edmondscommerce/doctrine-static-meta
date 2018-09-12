<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelationAbstract;

/**
 * Trait HasLargeRelationUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One LargeRelation
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits\HasLargeRelation
 */
// phpcs:enable
trait HasLargeRelationUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            LargeRelation::getDoctrineStaticMeta()->getSingular(),
            LargeRelation::class
        );
    }
}
