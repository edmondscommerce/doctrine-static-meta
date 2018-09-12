<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClient;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClientAbstract;

/**
 * Trait HasAnotherDeeplyNestedClientUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One AnotherDeeplyNestedClient
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\AnotherDeeplyNestedClient\Traits\HasAnotherDeeplyNestedClient
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientUnidirectionalOneToOne
{
    use HasAnotherDeeplyNestedClientAbstract;

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
        $builder->addOwningOneToOne(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getSingular(),
            AnotherDeeplyNestedClient::class
        );
    }
}
