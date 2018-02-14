<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface HasProduct
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Product
     */
    public function getProduct(): ?Product;

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeProduct(): UsesPHPMetaDataInterface;
}
