<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits\HasAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Address\Traits\HasAddressesAbstract;
use My\Test\Project\Entities\Address;

/**
 * Trait HasAddressesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Address.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddresses
 */
trait HasAddressesUnidirectionalOneToMany
{
    use HasAddressesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForAddresses(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Address::getPlural(),
            Address::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.Address::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Address::getSingular().'_'.Address::getIdField(),
            Address::getIdField()
        );
        $manyToManyBuilder->build();
    }
}