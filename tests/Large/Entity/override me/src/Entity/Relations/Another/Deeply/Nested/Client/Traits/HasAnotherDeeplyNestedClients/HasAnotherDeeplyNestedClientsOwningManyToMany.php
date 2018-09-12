<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClients;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClientsAbstract;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\ReciprocatesAnotherDeeplyNestedClient;

/**
 * Trait HasAnotherDeeplyNestedClientsOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and AnotherDeeplyNestedClient
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\AnotherDeeplyNestedClient\Traits\HasAnotherDeeplyNestedClients
 */
// phpcs:enable
trait HasAnotherDeeplyNestedClientsOwningManyToMany
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

        $manyToManyBuilder = $builder->createManyToMany(
            AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getPlural(),
            AnotherDeeplyNestedClient::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                AnotherDeeplyNestedClient::getDoctrineStaticMeta()->getSingular() . '_' . AnotherDeeplyNestedClient::PROP_ID
            ),
            AnotherDeeplyNestedClient::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
