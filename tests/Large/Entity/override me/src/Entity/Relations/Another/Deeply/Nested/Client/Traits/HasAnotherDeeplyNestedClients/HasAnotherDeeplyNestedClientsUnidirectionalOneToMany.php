<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClients;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClientsAbstract;

/**
 * Trait HasAnotherDeeplyNestedClientsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to AnotherDeeplyNestedClient.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AnotherDeeplyNestedClient\HasAnotherDeeplyNestedClients
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientsUnidirectionalOneToMany
{
    use HasAnotherDeeplyNestedClientsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAnotherDeeplyNestedClients(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getPlural(),
            AnotherDeeplyNestedClient::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular().'_'.static::PROP_ID,
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getSingular().'_'.AnotherDeeplyNestedClient::PROP_ID,
            AnotherDeeplyNestedClient::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
