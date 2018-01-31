<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Address\Traits\HasAddress;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address;
use  My\Test\Project\Entities\Relations\Address\Traits\HasAddressAbstract;

trait HasAddressInverseOneToOne
{
    use HasAddressAbstract;

    use ReciprocatesAddress;

    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            Address::getSingular(),
            Address::class,
            static::getSingular()
        );
    }
}
