<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperties;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertiesAbstract;
use My\Test\Project\Entity\Relations\Large\Property\Traits\ReciprocatesLargeProperty;

/**
 * Trait HasLargePropertiesOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to LargeProperty.
 *
 * The LargeProperty has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeProperty\HasLargeProperties
 */
// phpcs:enable
trait HasLargePropertiesOneToMany
{
    use HasLargePropertiesAbstract;

    use ReciprocatesLargeProperty;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForLargeProperties(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            LargeProperty::getDoctrineStaticMeta()->getPlural(),
            LargeProperty::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
