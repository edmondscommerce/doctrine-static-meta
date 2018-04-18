<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product\Brand as ProductBrand;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\HasProductBrandInterface;
use My\Test\Project\Entity\Relations\Product\Brand\Interfaces\ReciprocatesProductBrandInterface;

trait HasProductBrandAbstract
{
    /**
     * @var ProductBrand|null
     */
    private $productBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForProductBrand(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForProductBrand(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasProductBrandInterface::PROPERTY_NAME_PRODUCT_BRAND,
            new Valid()
        );
    }

    /**
     * @return ProductBrand|null
     */
    public function getProductBrand(): ?ProductBrand
    {
        return $this->productBrand;
    }

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    ): self {
        if ($this instanceof ReciprocatesProductBrandInterface && true === $recip) {
            $this->reciprocateRelationOnProductBrand($productBrand);
        }
        $this->productBrand = $productBrand;

        return $this;
    }

    /**
     * @return self
     */
    public function removeProductBrand(): self
    {
        $this->productBrand = null;

        return $this;
    }
}
