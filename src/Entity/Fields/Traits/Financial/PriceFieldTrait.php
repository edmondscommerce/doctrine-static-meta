<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Financial;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Financial\PriceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait PriceFieldTrait
{
    /**
     * @var float|null
     */
    private $price;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForPrice(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDecimalFields(
            [PriceFieldInterface::PROP_PRICE],
            $builder,
            PriceFieldInterface::DEFAULT_PRICE
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForPrice(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            PriceFieldInterface::PROP_PRICE,
            new GreaterThanOrEqual(0)
        );
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        if (null === $this->price) {
            return PriceFieldInterface::DEFAULT_PRICE;
        }

        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return $this|PriceFieldInterface
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(PriceFieldInterface::PROP_PRICE);
        }

        return $this;
    }
}
