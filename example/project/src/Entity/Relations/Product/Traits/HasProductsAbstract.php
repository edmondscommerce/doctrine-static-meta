<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product as Product;
use My\Test\Project\Entity\Relations\Product\Interfaces\HasProductsInterface;
use My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;

trait HasProductsAbstract
{
    /**
     * @var ArrayCollection|Product[]
     */
    private $products;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForProducts(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasProductsInterface::PROPERTY_NAME_PRODUCTS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForProducts(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Collection|Product[] $products
     *
     * @return self
     */
    public function setProducts(Collection $products): self
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addProduct(
        Product $product,
        bool $recip = true
    ): self {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            if ($this instanceof ReciprocatesProductInterface && true === $recip) {
                $this->reciprocateRelationOnProduct($product);
            }
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeProduct(
        Product $product,
        bool $recip = true
    ): self {
        $this->products->removeElement($product);
        if ($this instanceof ReciprocatesProductInterface && true === $recip) {
            $this->removeRelationOnProduct($product);
        }

        return $this;
    }

    /**
     * Initialise the products property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initProducts()
    {
        $this->products = new ArrayCollection();

        return $this;
    }
}
