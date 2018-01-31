<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddresses;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Order\Address;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressesAbstract;

trait HasAddressesInverseManyToMany
{
    use HasAddressesAbstract;

    use ReciprocatesAddress;

    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Address::getPlural(), Address::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Address::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Address::getSingular() . '_' . Address::getIdField(),
            Address::getIdField()
        );
        $builder->build();
    }
}
