<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface HasProduct
{
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder);

    public function getProduct(): ?Product;

    public function setProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeProduct(): UsesPHPMetaDataInterface;

}
