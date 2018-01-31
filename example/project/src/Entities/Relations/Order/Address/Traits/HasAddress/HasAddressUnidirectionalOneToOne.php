<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddress;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressAbstract;

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
