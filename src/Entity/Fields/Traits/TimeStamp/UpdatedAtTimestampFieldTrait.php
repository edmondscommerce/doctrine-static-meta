<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\UpdatedAtTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Exception;

/**
 * Trait TimestampFieldTrait
 *
 * An Immutable updated timestamp. It is null until it is saved (and reloaded)
 *
 * Notice the use of a lifecyle event to handle setting the pre persist creation timestamp
 *
 * In test fixtures you will want to use something like
 * \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator::forceTimestamp
 * to provide consistent fixtures
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait UpdatedAtTimestampFieldTrait
{
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updatedAtTimestamp;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForUpdatedAtTimestamp(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent('prePersistUpdatedAtTimestamp', Events::preUpdate);
        $builder->addLifecycleEvent('prePersistUpdatedAtTimestamp', Events::prePersist);
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => UpdatedAtTimestampFieldInterface::PROP_UPDATED_AT_TIMESTAMP,
                'type'      => Type::DATETIME_IMMUTABLE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                UpdatedAtTimestampFieldInterface::PROP_UPDATED_AT_TIMESTAMP
            ))
            ->nullable(false)
            ->build();
    }

    /**
     * @throws Exception
     */
    public function prePersistUpdatedAtTimestamp(): void
    {
        $this->updatePropertyValue(
            UpdatedAtTimestampFieldInterface::PROP_UPDATED_AT_TIMESTAMP,
            new DateTimeImmutable()
        );
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAtTimestamp(): ?DateTimeImmutable
    {
        return $this->updatedAtTimestamp;
    }
}
