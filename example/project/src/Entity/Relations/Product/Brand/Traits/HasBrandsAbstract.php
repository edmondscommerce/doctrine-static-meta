<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product\Brand;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasBrandsInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesBrandInterface;

trait HasBrandsAbstract
{
    /**
     * @var ArrayCollection|Brand[]
     */
    private $brands;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForBrands(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasBrandsInterface::PROPERTY_NAME_BRANDS, new Valid());
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForBrands(ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Brand[]
     */
    public function getBrands(): Collection
    {
        return $this->brands;
    }

    /**
     * @param Collection|Brand[] $brands
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setBrands(Collection $brands): UsesPHPMetaDataInterface
    {
        $this->brands = $brands;

        return $this;
    }

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->brands->contains($brand)) {
            $this->brands->add($brand);
            if ($this instanceof ReciprocatesBrandInterface && true === $recip) {
                $this->reciprocateRelationOnBrand($brand);
            }
        }

        return $this;
    }

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->brands->removeElement($brand);
        if ($this instanceof ReciprocatesBrandInterface && true === $recip) {
            $this->removeRelationOnBrand($brand);
        }

        return $this;
    }

    /**
     * Initialise the brands property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initBrands()
    {
        $this->brands = new ArrayCollection();

        return $this;
    }
}
