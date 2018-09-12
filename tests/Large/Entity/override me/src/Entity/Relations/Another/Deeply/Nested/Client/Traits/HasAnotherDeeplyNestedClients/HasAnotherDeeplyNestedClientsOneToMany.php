<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClients;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClientsAbstract;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\ReciprocatesAnotherDeeplyNestedClient;

/**
 * Trait HasAnotherDeeplyNestedClientsOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to AnotherDeeplyNestedClient.
 *
 * The AnotherDeeplyNestedClient has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AnotherDeeplyNestedClient\HasAnotherDeeplyNestedClients
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientsOneToMany
{
    use HasAnotherDeeplyNestedClientsAbstract;

    use ReciprocatesAnotherDeeplyNestedClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAnotherDeeplyNestedClients(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getPlural(),
            AnotherDeeplyNestedClient::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
