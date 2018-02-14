<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface HasProducts
{
    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder);

    public function getProducts(): Collection;

    public function setProducts(Collection $products): UsesPHPMetaDataInterface;

    public function addProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;

}
