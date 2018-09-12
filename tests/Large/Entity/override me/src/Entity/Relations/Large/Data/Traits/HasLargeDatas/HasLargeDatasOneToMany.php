<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDatas;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDatasAbstract;
use My\Test\Project\Entity\Relations\Large\Data\Traits\ReciprocatesLargeData;

/**
 * Trait HasLargeDatasOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to LargeData.
 *
 * The LargeData has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeData\HasLargeDatas
 */
// phpcs:enable
trait HasLargeDatasOneToMany
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
        $builder->addOneToMany(
            LargeData::getDoctrineStaticMeta()->getPlural(),
            LargeData::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
