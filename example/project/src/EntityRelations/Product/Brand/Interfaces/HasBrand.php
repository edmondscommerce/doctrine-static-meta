<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Brand\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface HasBrand
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Brand
     */
    public function getBrand(): ?Brand;

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeBrand(): UsesPHPMetaDataInterface;
}
