<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeRequiredFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\AlwaysValidInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use RuntimeException;

/**
 * Trait DateTimeRequiredFieldTrait
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait DateTimeRequiredFieldTrait
{

    /**
     * @var DateTimeImmutable
     */
    private $dateTimeRequired;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDateTimeRequired(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => DateTimeRequiredFieldInterface::PROP_DATE_TIME_REQUIRED,
                'type'      => Types::DATETIME_IMMUTABLE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                DateTimeRequiredFieldInterface::PROP_DATE_TIME_REQUIRED
            ))
            ->nullable(false)
            ->build();
    }

    /**
     * Will return the beginning of Unix time without updating the property value. This is by convention the expected
     * way to present a null date
     *
     * @return DateTimeImmutable
     */
    public function getDateTimeRequired(): DateTimeImmutable
    {
        if (null === $this->dateTimeRequired) {
            return new DateTimeImmutable(DateTimeRequiredFieldInterface::DEFAULT_DATE_TIME_REQUIRED_DATE_STRING);
        }

        return $this->dateTimeRequired;
    }

    private function initDateTimeRequired(): void
    {
        $this->dateTimeRequired = new DateTimeImmutable(
            DateTimeRequiredFieldInterface::DEFAULT_DATE_TIME_REQUIRED_DATE_STRING
        );
    }

    /**
     * @param DateTimeImmutable $dateTimeRequired
     *
     * @return self
     */
    private function setDateTimeRequired(DateTimeImmutable $dateTimeRequired): self
    {
        /**  @var $this ImplementNotifyChangeTrackingPolicy */
        $this->updatePropertyValue(
            DateTimeRequiredFieldInterface::PROP_DATE_TIME_REQUIRED,
            $dateTimeRequired
        );

        return $this;
    }
}
