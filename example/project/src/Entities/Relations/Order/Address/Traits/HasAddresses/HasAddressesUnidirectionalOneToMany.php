<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressesAbstract;

/**
 * Trait HasAddressesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Address.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddresses
 */
trait HasAddressesUnidirectionalOneToMany
{
    use HasAddressesAbstract;

    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Address::getPlural(),
            Address::class
        );
    }
}
