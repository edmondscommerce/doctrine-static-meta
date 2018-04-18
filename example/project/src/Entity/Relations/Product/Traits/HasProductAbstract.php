<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Product as Product;
use  My\Test\Project\Entity\Relations\Product\Interfaces\HasProductInterface;
use  My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;


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
    abstract public static function getPropertyDoctrineMetaForProduct(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForProduct(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasProductInterface::PROPERTY_NAME_PRODUCT,
            new Valid()
        );
    }

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
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setProduct(
        Product $product,
        bool $recip = true
    ): self {
        if ($this instanceof ReciprocatesProductInterface && true === $recip) {
            $this->reciprocateRelationOnProduct($product);
        }
        $this->product = $product;

        return $this;
    }

    /**
     * @return self
     */
    public function removeProduct(): self
    {
        $this->product = null;

        return $this;
    }
}
