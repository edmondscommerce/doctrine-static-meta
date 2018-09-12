<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperty;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertyAbstract;



/**
 * Trait HasLargePropertyManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of LargeProperty
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeProperty\HasLargeProperty
 */
// phpcs:enable
trait HasLargePropertyUnidirectionalManyToOne
{
    use HasLargePropertyAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeProperty(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            LargeProperty::getDoctrineStaticMeta()->getSingular(),
            LargeProperty::class
        );
    }
}
