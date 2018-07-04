<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\CreationTimestampFieldInterface;
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
trait CreationTimestampFieldTrait
{

    /**
     * @var \DateTimeImmutable|null
     */
    private $creationTimestamp;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForCreationTimestamp(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent('prePersistCreationTimestamp', Events::prePersist);
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => CreationTimestampFieldInterface::PROP_CREATION_TIMESTAMP,
                'type'      => Type::TIME_IMMUTABLE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                CreationTimestampFieldInterface::PROP_CREATION_TIMESTAMP
            ))
            ->nullable(false)
            ->build();
    }

    /**
     * @throws \Exception
     */
    public function prePersistCreationTimestamp(): void
    {
        if (null === $this->creationTimestamp) {
            $this->creationTimestamp = new \DateTimeImmutable();
        }
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreationTimestamp(): ?\DateTimeImmutable
    {
        return $this->creationTimestamp;
    }
}
