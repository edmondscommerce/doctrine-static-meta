<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Address\Traits\HasAddresses;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address;
use  My\Test\Project\Entities\Relations\Address\Traits\HasAddressesAbstract;

trait HasAddressesOwningManyToMany
{
    use HasAddressesAbstract;

    use ReciprocatesAddress;

    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            Address::getPlural(), Address::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . Address::getPlural());
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
