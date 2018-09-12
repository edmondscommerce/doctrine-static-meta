<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClient;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClientAbstract;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\ReciprocatesAnotherDeeplyNestedClient;

/**
 * Trait HasAnotherDeeplyNestedClientManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of AnotherDeeplyNestedClient.
 *
 * AnotherDeeplyNestedClient has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AnotherDeeplyNestedClient\HasAnotherDeeplyNestedClient
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientManyToOne
{
    use HasAnotherDeeplyNestedClientAbstract;

    use ReciprocatesAnotherDeeplyNestedClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAnotherDeeplyNestedClient(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getSingular(),
            AnotherDeeplyNestedClient::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
