<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\FloatWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait FloatWithinRangeFieldTrait
{

    /**
     * @var float|null
     */
    private $floatWithinRange;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForFloatWithinRange(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleFloatFields(
            [FloatWithinRangeFieldInterface::PROP_FLOAT_WITHIN_RANGE],
            $builder,
            FloatWithinRangeFieldInterface::DEFAULT_FLOAT_WITHIN_RANGE
        );
    }

    /**
     * @return float|null
     */
    public function getFloatWithinRange(): ?float
    {
        if (null === $this->floatWithinRange) {
            return FloatWithinRangeFieldInterface::DEFAULT_FLOAT_WITHIN_RANGE;
        }

        return $this->floatWithinRange;
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyFloatWithinRange(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            FloatWithinRangeFieldInterface::PROP_FLOAT_WITHIN_RANGE,
            new Range(
                [
                    'min'        => FloatWithinRangeFieldInterface::MIN_FLOAT_WITHIN_RANGE,
                    'max'        => FloatWithinRangeFieldInterface::MAX_FLOAT_WITHIN_RANGE,
                    'minMessage' => FloatWithinRangeFieldInterface::MIN_MESSAGE_FLOAT_WITHIN_RANGE,
                    'maxMessage' => FloatWithinRangeFieldInterface::MAX_MESSAGE_FLOAT_WITHIN_RANGE,
                ]
            )
        );
    }

    /**
     * @param float|null $floatWithinRange
     *
     * @return self
     */
    private function setFloatWithinRange(?float $floatWithinRange): self
    {
        $this->updatePropertyValue(
            FloatWithinRangeFieldInterface::PROP_FLOAT_WITHIN_RANGE,
            $floatWithinRange
        );

        return $this;
    }
}
