<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeData;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDataAbstract;

/**
 * Trait HasLargeDataUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One LargeData
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits\HasLargeData
 */
// phpcs:enable
trait HasLargeDataUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            LargeData::getDoctrineStaticMeta()->getSingular(),
            LargeData::class
        );
    }
}
