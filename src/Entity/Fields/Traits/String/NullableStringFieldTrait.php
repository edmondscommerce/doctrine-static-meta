<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\NullableStringFieldInterface;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait NullableStringFieldTrait
{

    /**
     * @var string|null
     */
    private $nullableString;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForNullableString(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [NullableStringFieldInterface::PROP_NULLABLE_STRING],
            $builder,
            NullableStringFieldInterface::DEFAULT_NULLABLE_STRING,
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
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForNullableString(ValidatorClassMetaData $metadata): void
    {
        //        $metadata->addPropertyConstraint(
        //            NullableStringFieldInterface::PROP_NULLABLE_STRING,
        //            new NotBlank()
        //        );
    }

    /**
     * @return string|null
     */
    public function getNullableString(): ?string
    {
        if (null === $this->nullableString) {
            return NullableStringFieldInterface::DEFAULT_NULLABLE_STRING;
        }

        return $this->nullableString;
    }

    /**
     * @param string|null $nullableString
     *
     * @return self
     */
    public function setNullableString(?string $nullableString): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            NullableStringFieldInterface::PROP_NULLABLE_STRING,
            $nullableString
        );

        return $this;
    }
}
