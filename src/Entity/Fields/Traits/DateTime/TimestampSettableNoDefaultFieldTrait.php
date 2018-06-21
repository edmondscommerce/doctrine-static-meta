<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\TimestampSettableNoDefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Trait TimestampSettableNoDefaultFieldTrait
 *
 * This field is a timestamp that you can set and update the value as you see fit with no defaults
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime
 */
trait TimestampSettableNoDefaultFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $timestampSettableNoDefault;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForTimestampSettableNoDefault(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleDatetimeFields(
            [TimestampSettableNoDefaultFieldInterface::PROP_TIMESTAMP_SETTABLE_NO_DEFAULT],
            $builder,
            TimestampSettableNoDefaultFieldInterface::DEFAULT_TIMESTAMP_SETTABLE_NO_DEFAULT
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestampSettableNoDefault(): ?\DateTime
    {
        if (null === $this->timestampSettableNoDefault) {
            return TimestampSettableNoDefaultFieldInterface::DEFAULT_TIMESTAMP_SETTABLE_NO_DEFAULT;
        }

        return $this->timestampSettableNoDefault;
    }

    /**
     * @param \DateTime|null $timestampSettableNoDefault
     *
     * @return self
     */
    public function setTimestampSettableNoDefault(?\DateTime $timestampSettableNoDefault): self
    {
        $this->timestampSettableNoDefault = $timestampSettableNoDefault;

        return $this;
    }
}
