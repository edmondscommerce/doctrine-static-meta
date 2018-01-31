<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Order\Address;
use  My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddressAbstract;

/**
 * Trait HasAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Address.
 *
 * Address has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Address\HasAddress
 */
trait HasAddressManyToOne
{
    use HasAddressAbstract;

    use ReciprocatesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Address::getSingular(),
            Address::class,
            static::getPlural()
        );
    }
}
