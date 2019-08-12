<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IndexedUniqueIntegerFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

// phpcs:enable

trait IndexedUniqueIntegerFieldTrait
{
    /**
     * @var int
     */
    private $indexedUniqueInteger;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForIndexedUniqueInteger(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => IndexedUniqueIntegerFieldInterface::PROP_INDEXED_UNIQUE_INTEGER,
                'type' => Type::INTEGER,
            ]
        );
        $fieldBuilder
            ->columnName(
                MappingHelper::getColumnNameForField(
                    IndexedUniqueIntegerFieldInterface::PROP_INDEXED_UNIQUE_INTEGER
                )
            )
            ->nullable(false)
            ->unique(true)
            ->build();
    }

    /**
     * @return int
     */
    public function getIndexedUniqueInteger(): int
    {
        return $this->indexedUniqueInteger;
    }

    /**
     * @param int $indexedUniqueInteger
     *
     * @return self
     */
    private function setIndexedUniqueInteger(int $indexedUniqueInteger): self
    {
        $this->updatePropertyValue(
            IndexedUniqueIntegerFieldInterface::PROP_INDEXED_UNIQUE_INTEGER,
            $indexedUniqueInteger
        );

        return $this;
    }

    private function initIndexedUniqueInteger(): void
    {
        $this->indexedUniqueInteger = IndexedUniqueIntegerFieldInterface::DEFAULT_INDEXED_UNIQUE_INTEGER;
    }
}
