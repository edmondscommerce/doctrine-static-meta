<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface HasProducts
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection;

    /**
     * @param Collection|Product[] $products
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setProducts(Collection $products): UsesPHPMetaDataInterface;

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;
}
