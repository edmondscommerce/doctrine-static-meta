<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product\Brand;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesBrandInterface;

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
    abstract public static function getPropertyDoctrineMetaForBrand(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForBrands(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasBrandInterface::PROPERTY_NAME_BRAND, new Valid());
    }

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
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesBrandInterface && true === $recip) {
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
