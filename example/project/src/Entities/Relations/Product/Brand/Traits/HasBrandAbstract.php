<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Product\Brand\Interfaces\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;

trait HasBrandAbstract
{
    /**
     * @var Brand|null
     */
    private $brand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder): void;

    /**
     * @return Brand|null
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesBrand && true === $recip) {
            $this->reciprocateRelationOnBrand($brand);
        }
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeBrand(): UsesPHPMetaDataInterface
    {
        $this->brand = null;

        return $this;
    }
}
