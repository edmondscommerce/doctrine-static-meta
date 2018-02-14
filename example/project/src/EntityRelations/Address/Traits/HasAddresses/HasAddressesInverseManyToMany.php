<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Address\Traits\HasAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Address\Traits\HasAddressesAbstract;
use My\Test\Project\EntityRelations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address;

trait HasAddressesInverseManyToMany
{
    use HasAddressesAbstract;

    use ReciprocatesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Address::getPlural(),
            Address::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Address::getPlural().'_to_'.static::getPlural());
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
