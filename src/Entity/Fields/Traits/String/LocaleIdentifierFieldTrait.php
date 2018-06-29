<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait LocaleIdentifierFieldTrait
{

    /**
     * @var string|null
     */
    private $localeIdentifier;

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
            ->nullable(LocaleIdentifierFieldInterface::DEFAULT_LOCALE_IDENTIFIER === null)
            ->unique(false)
            ->length(50)
            ->build();
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
    protected static function validatorMetaForLocaleIdentifier(ValidatorClassMetaData $metadata)
    {
        //        $metadata->addPropertyConstraint(
        //            LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER,
        //            new NotBlank()
        //        );
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
    public function setLocaleIdentifier(?string $localeIdentifier): self
    {
        $this->updatePropertyValueAndNotify(
            LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER,
            $localeIdentifier
        );
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER);
        }

        return $this;
    }
}
