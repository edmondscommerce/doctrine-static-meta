<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Bic;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable

/**
 * Trait BusinessIdentifierCodeFieldTrait
 *
 * A Business Identifier Code also known as SWIFT-BIC, BIC, SWIFT ID or SWIFT code
 *
 * @see     https://en.wikipedia.org/wiki/ISO_9362
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String
 */
trait BusinessIdentifierCodeFieldTrait
{

    /**
     * @var string|null
     */
    private ?string $businessIdentifierCode;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForBusinessIdentifierCode(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE,
                'type'      => Type::STRING,
                'default'   => BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE
            ))
            ->nullable()
            ->unique(false)
            ->length(20)
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
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyBusinessIdentifierCode(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE,
            [
                new Bic(),
                new Length(
                    [
                        'min' => 0,
                        'max' => 20,
                    ]
                ),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getBusinessIdentifierCode(): ?string
    {
        if (null === $this->businessIdentifierCode) {
            return BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE;
        }

        return $this->businessIdentifierCode;
    }

    /**
     * @param string|null $businessIdentifierCode
     *
     * @return self
     */
    private function setBusinessIdentifierCode(?string $businessIdentifierCode): self
    {
        $this->updatePropertyValue(
            BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE,
            $businessIdentifierCode
        );

        return $this;
    }
}
