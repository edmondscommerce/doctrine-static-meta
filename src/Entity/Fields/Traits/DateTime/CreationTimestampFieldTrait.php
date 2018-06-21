<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\CreationTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait TimestampFieldTrait
 *
 * An Immutable creation timestamp
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait CreationTimestampFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $timestamp;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForTimestamp(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => CreationTimestampFieldInterface::PROP_TIMESTAMP,
                'type'      => Type::DATETIME_IMMUTABLE,
                'default'   => MappingHelper::DATETIME_DEFAULT_CURRENT_TIME_STAMP,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(CreationTimestampFieldInterface::PROP_TIMESTAMP))
            ->nullable(null)
            ->build();
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }
}
