<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperty;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargePropertyAbstract;
use My\Test\Project\Entity\Relations\Large\Property\Traits\ReciprocatesLargeProperty;

/**
 * Trait HasLargePropertyOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and LargeProperty
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\LargeProperty\Traits\HasLargeProperty
 */
// phpcs:enable
trait HasLargePropertyOwningOneToOne
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
        $builder->addOwningOneToOne(
            LargeProperty::getDoctrineStaticMeta()->getSingular(),
            LargeProperty::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
