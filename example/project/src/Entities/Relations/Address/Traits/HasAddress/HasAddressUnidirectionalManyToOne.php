<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Address;
use  My\Test\Project\Entities\Relations\Address\Traits\HasAddressAbstract;

/**
 * Trait HasAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddress
 */
trait HasAddressUnidirectionalManyToOne
{
    use HasAddressAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Address::getSingular(),
            Address::class
        );
    }
}
