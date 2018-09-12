<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClients;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientsAbstract;
use My\Test\Project\Entity\Relations\Some\Client\Traits\ReciprocatesSomeClient;

/**
 * Trait HasSomeClientsOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and SomeClient
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits\HasSomeClients
 */
// phpcs:enable
trait HasSomeClientsOwningManyToMany
{
    use HasSomeClientsAbstract;

    use ReciprocatesSomeClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForSomeClients(
        ClassMetadataBuilder $builder
    ): void {

        $manyToManyBuilder = $builder->createManyToMany(
            SomeClient::getDoctrineStaticMeta()->getPlural(),
            SomeClient::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(SomeClient::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                SomeClient::getDoctrineStaticMeta()->getSingular() . '_' . SomeClient::PROP_ID
            ),
            SomeClient::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
