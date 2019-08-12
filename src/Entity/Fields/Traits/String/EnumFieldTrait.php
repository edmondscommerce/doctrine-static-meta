<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait EnumFieldTrait
{

    /**
     * @var string
     */
    private $enum;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForEnum(ClassMetadataBuilder $builder): void
    {
        $columnName   = MappingHelper::getColumnNameForField(
            EnumFieldInterface::PROP_ENUM
        );
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => EnumFieldInterface::PROP_ENUM,
                'type'      => MappingHelper::TYPE_STRING,
                'default'   => EnumFieldInterface::DEFAULT_ENUM,
            ]
        );
        $fieldBuilder
            ->columnName($columnName)
            ->nullable(false)
            ->unique(false)
            ->length(50)
            ->build();

        $builder->addIndex([$columnName], $columnName . '_idx');
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
    protected static function validatorMetaForPropertyEnum(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            EnumFieldInterface::PROP_ENUM,
            [
                new Choice(EnumFieldInterface::ENUM_OPTIONS),
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
    public function getEnum(): string
    {
        if (null === $this->enum) {
            return EnumFieldInterface::DEFAULT_ENUM;
        }

        return $this->enum;
    }

    /**
     * Uses the Symfony Validator and fails back to basic in_array validation with exception
     *
     * @param string $enum
     *
     * @return self
     */
    private function setEnum(string $enum): self
    {
        $this->updatePropertyValue(
            EnumFieldInterface::PROP_ENUM,
            $enum
        );

        return $this;
    }

    private function initEnum(): void
    {
        $this->enum = EnumFieldInterface::DEFAULT_ENUM;
    }
}
