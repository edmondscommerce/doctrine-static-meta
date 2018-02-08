<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\Entities\Product;

trait HasProductsAbstract
{
    /**
     * @var ArrayCollection|Product[]
     */
    private $products;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForProducts(ClassMetadataBuilder $manyToManyBuilder): void;

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Collection $products
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setProducts(Collection $products): UsesPHPMetaDataInterface
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function addProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            if ($this instanceof ReciprocatesProduct && true === $recip) {
                $this->reciprocateRelationOnProduct($product);
            }
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeProduct(Product $product, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->products->removeElement($product);
        if ($this instanceof ReciprocatesProduct && true === $recip) {
            $this->removeRelationOnProduct($product);
        }

        return $this;
    }

    /**
     * Initialise the products property as a Doctrine ArrayCollection
     *
     * @return $this
     */
    private function initProducts()
    {
        $this->products = new ArrayCollection();

        return $this;
    }
}
