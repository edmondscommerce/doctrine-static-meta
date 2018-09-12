<?php declare(strict_types=1);
// phpcs:disable

namespace My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeData;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDataAbstract;
use My\Test\Project\Entity\Relations\Large\Data\Traits\ReciprocatesLargeData;

/**
 * Trait HasLargeDataInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and LargeData
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits\HasLargeData
 */
// phpcs:enable
trait HasLargeDataInverseOneToOne
{
    use HasLargeDataAbstract;

    use ReciprocatesLargeData;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeData(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addInverseOneToOne(
            LargeData::getDoctrineStaticMeta()->getSingular(),
            LargeData::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
