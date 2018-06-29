<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableOnceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait DateTimeSettableOnceFieldTrait
 *
 * This field is a dateTime that will be null until you set it. Once set (and possibly saved) it can not be updated
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait DateTimeSettableOnceFieldTrait
{

    /**
     * @var \DateTimeImmutable|null
     */
    private $dateTimeSettableOnce;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDateTimeSettableOnce(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE,
                'type'      => Type::DATETIME_IMMUTABLE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE
            ))
            ->nullable(DateTimeSettableOnceFieldInterface::DEFAULT_DATE_TIME_SETTABLE_ONCE === null)
            ->build();
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateTimeSettableOnce(): ?\DateTimeImmutable
    {
        return $this->dateTimeSettableOnce;
    }

    /**
     * @param \DateTimeImmutable $dateTimeSettableOnce
     *
     * @return self
     */
    public function setDateTimeSettableOnce(\DateTimeImmutable $dateTimeSettableOnce): self
    {
        if (null !== $this->dateTimeSettableOnce) {
            throw new \RuntimeException(
                DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE
                .' is already set, you can not overwrite this with a new dateTime'
            );
        }
        $this->updatePropertyValueAndNotify(
            DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE,
            $dateTimeSettableOnce
        );

        return $this;
    }
}
