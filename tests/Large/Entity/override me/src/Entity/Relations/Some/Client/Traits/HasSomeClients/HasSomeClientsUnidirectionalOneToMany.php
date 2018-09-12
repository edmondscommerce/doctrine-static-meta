<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClients;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientsAbstract;

/**
 * Trait HasSomeClientsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to SomeClient.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\SomeClient\HasSomeClients
 */
// phpcs:enable
trait HasSomeClientsUnidirectionalOneToMany
{
    use HasSomeClientsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForSomeClients(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            SomeClient::getDoctrineStaticMeta()->getPlural(),
            SomeClient::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(SomeClient::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular().'_'.static::PROP_ID,
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            SomeClient::getDoctrineStaticMeta()->getSingular().'_'.SomeClient::PROP_ID,
            SomeClient::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
