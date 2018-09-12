<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDatas;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDatasAbstract;
use My\Test\Project\Entity\Relations\Large\Data\Traits\ReciprocatesLargeData;

/**
 * Trait HasLargeDatasOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and LargeData
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits\HasLargeDatas
 */
// phpcs:enable
trait HasLargeDatasOwningManyToMany
{
    use HasLargeDatasAbstract;

    use ReciprocatesLargeData;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeDatas(
        ClassMetadataBuilder $builder
    ): void {

        $manyToManyBuilder = $builder->createManyToMany(
            LargeData::getDoctrineStaticMeta()->getPlural(),
            LargeData::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(LargeData::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                LargeData::getDoctrineStaticMeta()->getSingular() . '_' . LargeData::PROP_ID
            ),
            LargeData::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
