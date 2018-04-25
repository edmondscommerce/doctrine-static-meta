<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Address\Traits\HasAddresses;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Address\Traits\HasAddressesAbstract;
use  My\Test\Project\Entity\Relations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address as Address;

trait HasAddressesInverseManyToMany
{
    use HasAddressesAbstract;

    use ReciprocatesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForAddresses(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Address::getPlural(), Address::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $fromTableName = Inflector::tableize(Address::getPlural());
        $toTableName   = Inflector::tableize(static::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Address::getSingular().'_'.Address::getIdField(),
            Address::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
