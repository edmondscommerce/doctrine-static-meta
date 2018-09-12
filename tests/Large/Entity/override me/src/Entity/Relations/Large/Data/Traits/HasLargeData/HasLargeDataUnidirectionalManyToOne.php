<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeData;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDataAbstract;



/**
 * Trait HasLargeDataManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of LargeData
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeData\HasLargeData
 */
// phpcs:enable
trait HasLargeDataUnidirectionalManyToOne
{
    use HasLargeDataAbstract;

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
        $builder->addManyToOne(
            LargeData::getDoctrineStaticMeta()->getSingular(),
            LargeData::class
        );
    }
}
