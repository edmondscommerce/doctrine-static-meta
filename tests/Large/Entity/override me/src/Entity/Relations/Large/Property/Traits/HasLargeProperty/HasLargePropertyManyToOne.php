<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperty;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertyAbstract;
use My\Test\Project\Entity\Relations\Large\Property\Traits\ReciprocatesLargeProperty;

/**
 * Trait HasLargePropertyManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of LargeProperty.
 *
 * LargeProperty has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\LargeProperty\HasLargeProperty
 */
// phpcs:enable
trait HasLargePropertyManyToOne
{
    use HasLargePropertyAbstract;

    use ReciprocatesLargeProperty;

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
            LargeProperty::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
