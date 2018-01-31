<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Address\Traits\HasAddress;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Address;
use  My\Test\Project\Entities\Relations\Address\Traits\HasAddressAbstract;

trait HasAddressUnidirectionalOneToOne
{
    use HasAddressAbstract;

    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Address::getSingular(),
            Address::class
        );
    }
}
