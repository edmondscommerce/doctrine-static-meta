<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\TimestampSettableOnceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait TimestampSettableOnceFieldTrait
 *
 * This field is a timestamp that will be null until you set it. Once set (and possibly saved) it can not be updated
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait TimestampSettableOnceFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $timestampSettableOnce;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForTimestampSettableOnce(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [TimestampSettableOnceFieldInterface::PROP_TIMESTAMP_SETTABLE_ONCE],
            $builder,
            TimestampSettableOnceFieldInterface::DEFAULT_TIMESTAMP_SETTABLE_ONCE
        );
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => TimestampSettableOnceFieldInterface::PROP_TIMESTAMP_SETTABLE_ONCE,
                'type'      => Type::DATETIME_IMMUTABLE,
                'default'   => MappingHelper::DATETIME_DEFAULT_CURRENT_TIME_STAMP,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(TimestampSettableOnceFieldInterface::PROP_TIMESTAMP_SETTABLE_ONCE))
            ->nullable(null)
            ->build();
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestampSettableOnce(): ?\DateTimeImmutable
    {
        return $this->timestampSettableOnce;
    }

    /**
     * @param \DateTime|null $timestampSettableOnce
     *
     * @return self
     */
    public function setTimestampSettableOnce(?\DateTime $timestampSettableOnce): self
    {
        if (null !== $this->timestampSettableOnce) {
            throw new \RuntimeException(TimestampSettableOnceFieldInterface::PROP_TIMESTAMP_SETTABLE_ONCE.' is already set, you can not overwrite this with a new timestamp');
        }
        $this->timestampSettableOnce = $timestampSettableOnce;

        return $this;
    }
}
