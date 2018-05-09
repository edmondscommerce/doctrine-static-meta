<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Attribute;

// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\ShippingFieldInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ShippingFieldTrait
{

    /**
     * @var float
     */
    private $shipping;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForShipping(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleFloatFields(
            [ShippingFieldInterface::PROP_SHIPPING],
            $builder,
            false
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ValidatorClassMetaData $metadata
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForShipping(ValidatorClassMetaData $metadata)
    {
        //        $metadata->addPropertyConstraint(
        //            ShippingFieldInterface::PROP_SHIPPING,
        //            new NotBlank()
        //        );
    }

    /**
     * @return float
     */
    public function getShipping(): float
    {
        return $this->shipping;
    }

    /**
     * @param float $shipping
     * @return self
     */
    public function setShipping(float $shipping): self
    {
        $this->shipping = $shipping;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(ShippingFieldInterface::PROP_SHIPPING);
        }
        return $this;
    }
}
