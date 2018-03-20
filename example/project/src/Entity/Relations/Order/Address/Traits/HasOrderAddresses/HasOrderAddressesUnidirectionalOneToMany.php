<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressesAbstract;
use My\Test\Project\Entities\Order\Address as OrderAddress;

/**
 * Trait HasOrderAddressesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to OrderAddress.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderAddress\HasOrderAddresses
 */
trait HasOrderAddressesUnidirectionalOneToMany
{
    use HasOrderAddressesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderAddresses(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            OrderAddress::getPlural(),
            OrderAddress::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.OrderAddress::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            OrderAddress::getSingular().'_'.OrderAddress::getIdField(),
            OrderAddress::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
