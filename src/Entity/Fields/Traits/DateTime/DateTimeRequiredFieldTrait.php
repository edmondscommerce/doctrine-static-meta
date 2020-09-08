<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeRequiredFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use RuntimeException;

/**
 * Trait DateTimeRequiredFieldTrait
 *
 * This field is a dateTime that will be null until you set it.
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait DateTimeRequiredFieldTrait
{

    /**
     * @var null|DateTimeImmutable
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
     * @return DateTimeImmutable
     */
    public function getDateTimeRequired(): DateTimeImmutable
    {
        if (null === $this->dateTimeRequired) {
            throw new RuntimeException('You must set a value for $this->dateTimeRequired before you can get it');
        }

        return $this->dateTimeRequired;
    }

    /**
     * @param DateTimeImmutable|null $dateTimeRequired
     *
     * @return self
     */
    private function setDateTimeRequired(DateTimeImmutable $dateTimeRequired): self
    {
        $this->updatePropertyValue(
            DateTimeRequiredFieldInterface::PROP_DATE_TIME_REQUIRED,
            $dateTimeRequired
        );

        return $this;
    }
}
