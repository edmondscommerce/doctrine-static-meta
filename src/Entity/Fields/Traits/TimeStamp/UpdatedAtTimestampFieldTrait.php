<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\UpdatedAtTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait TimestampFieldTrait
 *
 * An Immutable creation timestamp. It is null until it is saved (and reloaded)
 *
 * Notice the use of a lifecyle event to handle setting the pre persist creation timestamp
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait UpdatedAtTimestampFieldTrait
{
    /**
     * @var \DateTimeImmutable|null
     */
    private $updatedAtTimestamp;

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
     * @throws \Exception
     */
    public function prePersistUpdatedAtTimestamp(): void
    {
        $this->updatePropertyValue(
            UpdatedAtTimestampFieldInterface::PROP_UPDATED_AT_TIMESTAMP,
            new \DateTimeImmutable()
        );
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAtTimestamp(): ?\DateTimeImmutable
    {
        return $this->updatedAtTimestamp;
    }
}
