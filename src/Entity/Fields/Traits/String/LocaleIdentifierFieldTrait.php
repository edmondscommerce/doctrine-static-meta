<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait LocaleIdentifierFieldTrait
{

    /**
     * @var string|null
     */
    private ?string $localeIdentifier;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForLocaleIdentifier(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER,
                'type'      => Type::STRING,
                'default'   => LocaleIdentifierFieldInterface::DEFAULT_LOCALE_IDENTIFIER,
            ]
        );
        $fieldBuilder
            ->columnName(
                MappingHelper::getColumnNameForField(LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER)
            )
            ->nullable()
            ->unique(false)
            ->length(50)
            ->build();
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    protected static function validatorMetaForPropertyLocaleIdentifier(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER,
            [
                new Locale(['canonicalize' => true]),
                new Length(
                    [
                        'min' => 0,
                        'max' => 50,
                    ]
                )
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getLocaleIdentifier(): ?string
    {
        if (null === $this->localeIdentifier) {
            return LocaleIdentifierFieldInterface::DEFAULT_LOCALE_IDENTIFIER;
        }

        return $this->localeIdentifier;
    }

    /**
     * @param string|null $localeIdentifier
     *
     * @return self
     */
    private function setLocaleIdentifier(?string $localeIdentifier): self
    {
        $this->updatePropertyValue(
            LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER,
            $localeIdentifier
        );

        return $this;
    }
}
