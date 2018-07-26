<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait UniqueStringFieldTrait
{

    /**
     * @var string|null
     */
    private $uniqueString;

    /**
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForUniqueString(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => UniqueStringFieldInterface::PROP_UNIQUE_STRING,
                'type'      => Type::STRING,
                'default'   => UniqueStringFieldInterface::DEFAULT_UNIQUE_STRING,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(UniqueStringFieldInterface::PROP_UNIQUE_STRING))
            ->nullable(false)
            ->unique(true)
            ->length(Database::MAX_VARCHAR_LENGTH)
            ->build();
        $builder->addIndex(
            [
                MappingHelper::getColumnNameForField(UniqueStringFieldInterface::PROP_UNIQUE_STRING),
            ],
            MappingHelper::getColumnNameForField(UniqueStringFieldInterface::PROP_UNIQUE_STRING)
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
    protected static function validatorMetaForUniqueString(ValidatorClassMetaData $metadata): void
    {
        //        $metadata->addPropertyConstraint(
        //            UniqueStringFieldInterface::PROP_UNIQUE_STRING,
        //            new NotBlank()
        //        );
    }

    /**
     * @return string|null
     */
    public function getUniqueString(): ?string
    {
        if (null === $this->uniqueString) {
            return UniqueStringFieldInterface::DEFAULT_UNIQUE_STRING;
        }

        return $this->uniqueString;
    }

    /**
     * @param string|null $uniqueString
     *
     * @return self
     */
    public function setUniqueString(?string $uniqueString): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            UniqueStringFieldInterface::PROP_UNIQUE_STRING,
            $uniqueString
        );

        return $this;
    }
}
