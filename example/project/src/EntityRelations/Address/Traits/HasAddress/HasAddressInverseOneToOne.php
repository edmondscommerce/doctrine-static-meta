<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Address\Traits\HasAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Address\Traits\ReciprocatesAddress;
use My\Test\Project\Entities\Address;
use My\Test\Project\EntityRelations\Address\Traits\HasAddressAbstract;

trait HasAddressInverseOneToOne
{
    use HasAddressAbstract;

    use ReciprocatesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            Address::getSingular(),
            Address::class,
            static::getSingular()
        );
    }
}
