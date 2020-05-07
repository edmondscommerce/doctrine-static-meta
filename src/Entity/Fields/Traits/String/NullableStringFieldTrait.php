<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\NullableStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait NullableStringFieldTrait
{

    /**
     * @var string|null
     */
    private ?string $nullableString;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForNullableString(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [NullableStringFieldInterface::PROP_NULLABLE_STRING],
            $builder,
            NullableStringFieldInterface::DEFAULT_NULLABLE_STRING
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
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyNullableString(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            NullableStringFieldInterface::PROP_NULLABLE_STRING,
            new Length(
                [
                    'min' => 0,
                    'max' => Database::MAX_VARCHAR_LENGTH,
                ]
            )
        );
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
    private function setNullableString(?string $nullableString): self
    {
        $this->updatePropertyValue(
            NullableStringFieldInterface::PROP_NULLABLE_STRING,
            $nullableString
        );

        return $this;
    }
}
