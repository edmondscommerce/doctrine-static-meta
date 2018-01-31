<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressAbstract;

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
