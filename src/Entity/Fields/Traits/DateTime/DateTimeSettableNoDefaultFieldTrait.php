<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTimeImmutable;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableNoDefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait DateTimeSettableNoDefaultFieldTrait
 *
 * This field is a dateTime that you can set and update the value as you see fit with no defaults
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTimeImmutable
 */
trait DateTimeSettableNoDefaultFieldTrait
{

    /**
     * @var \DateTimeImmutable|null
     */
    private $dateTimeSettableNoDefault;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDateTimeSettableNoDefault(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [DateTimeSettableNoDefaultFieldInterface::PROP_DATE_TIME_SETTABLE_NO_DEFAULT],
            $builder,
            DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT
        );
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateTimeSettableNoDefault(): ?\DateTimeImmutable
    {
        if (null === $this->dateTimeSettableNoDefault) {
            return DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT;
        }

        return $this->dateTimeSettableNoDefault;
    }

    /**
     * @param \DateTimeImmutable|null $dateTimeSettableNoDefault
     *
     * @return self
     */
    private function setDateTimeSettableNoDefault(?\DateTimeImmutable $dateTimeSettableNoDefault): self
    {
        $this->updatePropertyValue(
            DateTimeSettableNoDefaultFieldInterface::PROP_DATE_TIME_SETTABLE_NO_DEFAULT,
            $dateTimeSettableNoDefault
        );

        return $this;
    }
}
