<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressesAbstract;
use  My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;
use My\Test\Project\Entities\Order\Address as OrderAddress;

trait HasOrderAddressesOwningManyToMany
{
    use HasOrderAddressesAbstract;

    use ReciprocatesOrderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderAddresses(ClassMetadataBuilder $builder): void
    {

        $manyToManyBuilder = $builder->createManyToMany(
            OrderAddress::getPlural(), OrderAddress::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.OrderAddress::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            OrderAddress::getSingular().'_'.OrderAddress::getIdField(),
            OrderAddress::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
