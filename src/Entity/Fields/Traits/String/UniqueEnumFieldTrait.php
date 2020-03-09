<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueEnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait UniqueEnumFieldTrait
{

    /**
     * @var string
     */
    private $uniqueEnum;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForUniqueEnum(ClassMetadataBuilder $builder): void
    {
        $columnName   = MappingHelper::getColumnNameForField(
            UniqueEnumFieldInterface::PROP_UNIQUE_ENUM
        );
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => UniqueEnumFieldInterface::PROP_UNIQUE_ENUM,
                'type'      => MappingHelper::TYPE_STRING,
                'default'   => UniqueEnumFieldInterface::DEFAULT_UNIQUE_ENUM,
            ]
        );
        $fieldBuilder
            ->columnName($columnName)
            ->nullable(false)
            ->unique(true)
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
    protected static function validatorMetaForPropertyUniqueEnum(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            UniqueEnumFieldInterface::PROP_UNIQUE_ENUM,
            [
                new Choice(UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS),
                new Length(
                    [
                        'min' => 1,
                        'max' => 50,
                    ]
                ),
            ]
        );
    }

    /**
     * @return string
     */
    public function getUniqueEnum(): string
    {
        if (null === $this->uniqueEnum) {
            return UniqueEnumFieldInterface::DEFAULT_UNIQUE_ENUM;
        }

        return $this->uniqueEnum;
    }

    /**
     * Uses the Symfony Validator and fails back to basic in_array validation with exception
     *
     * @param string $uniqueEnum
     *
     * @return self
     */
    private function setUniqueEnum(string $uniqueEnum): self
    {
        $this->updatePropertyValue(
            UniqueEnumFieldInterface::PROP_UNIQUE_ENUM,
            $uniqueEnum
        );

        return $this;
    }

    private function initUniqueEnum(): void
    {
        $this->uniqueEnum = UniqueEnumFieldInterface::DEFAULT_UNIQUE_ENUM;
    }
}
