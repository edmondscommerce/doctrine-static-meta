<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Length;
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
            ->length(UniqueStringFieldInterface::LENGTH_UNIQUE_STRING)
            ->build();
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
     *
     */
    protected static function validatorMetaForPropertyUniqueString(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            UniqueStringFieldInterface::PROP_UNIQUE_STRING,
            new Length(['min' => 0, 'max' => UniqueStringFieldInterface::LENGTH_UNIQUE_STRING])
        );
    }

    /**
     * @param string|null $uniqueString
     *
     * @return self
     */
    private function setUniqueString(?string $uniqueString): self
    {
        $this->updatePropertyValue(
            UniqueStringFieldInterface::PROP_UNIQUE_STRING,
            $uniqueString
        );

        return $this;
    }
}
