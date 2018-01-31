<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

trait HasBrandsAbstract
{
    /**
     * @var ArrayCollection|Brand[]
     */
    private $brands;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder);

    /**
     * @return Collection|Brand[]
     */
    public function getBrands(): Collection
    {
        return $this->brands;
    }

    /**
     * @param Collection $brands
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
     */
    public function addBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->brands->contains($brand)) {
            $this->brands->add($brand);
            if (true === $recip) {
                $this->reciprocateRelationOnBrand($brand, false);
            }
        }

        return $this;
    }

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->brands->removeElement($brand);
        if (true === $recip) {
            $this->removeRelationOnBrand($brand, false);
        }

        return $this;
    }

    private function initBrands()
    {
        $this->brands = new ArrayCollection();

        return $this;
    }
}
