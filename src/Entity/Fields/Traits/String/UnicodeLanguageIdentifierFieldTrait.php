<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UnicodeLanguageIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Language;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;


trait UnicodeLanguageIdentifierFieldTrait
{

    /**
     * @var string|null
     */
    private $unicodeLanguageIdentifier;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForUnicodeLanguageIdentifier(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER,
                'type'      => Type::STRING,
                'default'   => null,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER
            ))
            ->nullable(true)
            ->unique(false)
            ->length(50)
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
    protected static function validatorMetaForUnicodeLanguageIdentifier(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER,
            new Language()
        );
    }

    /**
     * @return string|null
     */
    public function getUnicodeLanguageIdentifier(): ?string
    {
        if (null === $this->unicodeLanguageIdentifier) {
            return UnicodeLanguageIdentifierFieldInterface::DEFAULT_UNICODE_LANGUAGE_IDENTIFIER;
        }

        return $this->unicodeLanguageIdentifier;
    }

    /**
     * @param string|null $unicodeLanguageIdentifier
     *
     * @return self
     */
    public function setUnicodeLanguageIdentifier(?string $unicodeLanguageIdentifier): self
    {
        $this->unicodeLanguageIdentifier = $unicodeLanguageIdentifier;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(
                UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER
            );
        }

        return $this;
    }
}
