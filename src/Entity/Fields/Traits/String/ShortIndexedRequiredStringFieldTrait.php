<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * This trait provides a short (50 characters) string which is non uniquely indexed and is required
 *
 * Trait ShortIndexedRequiredStringFieldTrait
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String
 */
trait ShortIndexedRequiredStringFieldTrait
{

    /**
     * @var string
     */
    private $shortIndexedRequiredString;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForShortIndexedRequiredString(ClassMetadataBuilder $builder): void
    {
        $columnName   = MappingHelper::getColumnNameForField(
            ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING
        );
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING,
                'type'      => Type::STRING,
                'default'   => ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING,
            ]
        );
        $fieldBuilder
            ->columnName($columnName)
            ->nullable(false)
            ->unique(false)
            ->length(ShortIndexedRequiredStringFieldInterface::LENGTH_SHORT_INDEXED_REQUIRED_STRING)
            ->build();

        $builder->addIndex([$columnName], $columnName . '_idx');
    }

    /**
     * @return string
     */
    public function getShortIndexedRequiredString(): string
    {
        if (null === $this->shortIndexedRequiredString) {
            return ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING;
        }

        return $this->shortIndexedRequiredString;
    }

    /**
     * @param ValidatorClassMetaData $metadata
     */
    protected static function validatorMetaForPropertyShortIndexedRequiredString(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING,
            [
                new NotEqualTo(ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING),
                new NotBlank(),
                new Length(
                    [
                        'min' => 1,
                        'max' => ShortIndexedRequiredStringFieldInterface::LENGTH_SHORT_INDEXED_REQUIRED_STRING,
                    ]
                ),
            ]
        );
    }

    /**
     * @param string|null $shortIndexedRequiredString
     *
     * @return self
     */
    private function setShortIndexedRequiredString(string $shortIndexedRequiredString): self
    {
        $this->updatePropertyValue(
            ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING,
            $shortIndexedRequiredString
        );

        return $this;
    }

    private function initShortIndexedRequiredString(): void
    {
        $this->shortIndexedRequiredString =
            ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING;
    }
}
