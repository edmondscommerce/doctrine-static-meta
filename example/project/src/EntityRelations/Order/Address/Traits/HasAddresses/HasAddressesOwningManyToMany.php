<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Order\Address\Traits\HasAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Order\Address\Traits\HasAddressesAbstract;
use My\Test\Project\EntityRelations\Order\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Order\Address;

trait HasAddressesOwningManyToMany
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
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.Address::getPlural());
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
