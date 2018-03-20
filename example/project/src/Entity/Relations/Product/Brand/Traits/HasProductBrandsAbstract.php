<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product\Brand as ProductBrand;
use  My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasProductBrandsInterface;
use  My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesProductBrandInterface;

trait HasProductBrandsAbstract
{
    /**
     * @var ArrayCollection|ProductBrand[]
     */
    private $productBrands;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForProductBrands(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasProductBrandsInterface::PROPERTY_NAME_PRODUCT_BRANDS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForProductBrands(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|ProductBrand[]
     */
    public function getProductBrands(): Collection
    {
        return $this->productBrands;
    }

    /**
     * @param Collection|ProductBrand[] $productBrands
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setProductBrands(Collection $productBrands): UsesPHPMetaDataInterface
    {
        $this->productBrands = $productBrands;

        return $this;
    }

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        if (!$this->productBrands->contains($productBrand)) {
            $this->productBrands->add($productBrand);
            if ($this instanceof ReciprocatesProductBrandInterface && true === $recip) {
                $this->reciprocateRelationOnProductBrand($productBrand);
            }
        }

        return $this;
    }

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        $this->productBrands->removeElement($productBrand);
        if ($this instanceof ReciprocatesProductBrandInterface && true === $recip) {
            $this->removeRelationOnProductBrand($productBrand);
        }

        return $this;
    }

    /**
     * Initialise the productBrands property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initProductBrands()
    {
        $this->productBrands = new ArrayCollection();

        return $this;
    }
}
