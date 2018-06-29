<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableNoDefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeSettableOnceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait DateTimeSettableNoDefaultFieldTrait
 *
 * This field is a dateTime that you can set and update the value as you see fit with no defaults
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait DateTimeSettableNoDefaultFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $dateTimeSettableNoDefault;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDateTimeSettableNoDefault(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleDatetimeFields(
            [DateTimeSettableNoDefaultFieldInterface::PROP_DATE_TIME_SETTABLE_NO_DEFAULT],
            $builder,
            DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getDateTimeSettableNoDefault(): ?\DateTime
    {
        if (null === $this->dateTimeSettableNoDefault) {
            return DateTimeSettableNoDefaultFieldInterface::DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT;
        }

        return $this->dateTimeSettableNoDefault;
    }

    /**
     * @param \DateTime|null $dateTimeSettableNoDefault
     *
     * @return self
     */
    public function setDateTimeSettableNoDefault(?\DateTime $dateTimeSettableNoDefault): self
    {
        $this->updatePropertyValueAndNotify(
            DateTimeSettableOnceFieldInterface::PROP_DATE_TIME_SETTABLE_ONCE,
            $dateTimeSettableNoDefault
        );

        return $this;
    }
}
