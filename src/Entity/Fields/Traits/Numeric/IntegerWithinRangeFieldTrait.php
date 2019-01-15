<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IntegerWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait IntegerWithinRangeFieldTrait
{

    /**
     * @var int|null
     */
    private $integerWithinRange;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForIntegerWithinRange(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE,
                'type'      => Type::INTEGER,
                'default'   => IntegerWithinRangeFieldInterface::DEFAULT_INTEGER_WITHIN_RANGE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE))
            ->nullable(true)
            ->unique(false)
            ->length(11)
            ->build();
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
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyIntegerWithinRange(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE,
            new Range(
                [
                    'min'        => IntegerWithinRangeFieldInterface::MIN_INTEGER_WITHIN_RANGE,
                    'max'        => IntegerWithinRangeFieldInterface::MAX_INTEGER_WITHIN_RANGE,
                    'minMessage' => IntegerWithinRangeFieldInterface::MIN_MESSAGE_INTEGER_WITHIN_RANGE,
                    'maxMessage' => IntegerWithinRangeFieldInterface::MAX_MESSAGE_INTEGER_WITHIN_RANGE
                ]
            )
        );
    }

    /**
     * @return int|null
     */
    public function getIntegerWithinRange(): ?int
    {
        if (null === $this->integerWithinRange) {
            return IntegerWithinRangeFieldInterface::DEFAULT_INTEGER_WITHIN_RANGE;
        }

        return $this->integerWithinRange;
    }

    /**
     * @param int|null $integerWithinRange
     *
     * @return self
     */
    private function setIntegerWithinRange(?int $integerWithinRange): self
    {
        $this->updatePropertyValue(
            IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE,
            $integerWithinRange
        );

        return $this;
    }
}
