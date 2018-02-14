<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\EntityRelations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\Entities\Product;

trait HasProductAbstract
{
    /**
     * @var Product|null
     */
    private $product;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void;

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesProduct && true === $recip) {
            $this->reciprocateRelationOnProduct($product);
        }
        $this->product = $product;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeProduct(): UsesPHPMetaDataInterface
    {
        $this->product = null;

        return $this;
    }
}
