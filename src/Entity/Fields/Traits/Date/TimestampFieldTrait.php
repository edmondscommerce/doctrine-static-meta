<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\TimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait TimestampFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $timestamp;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForTimestamp(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleDatetimeFields(
            [TimestampFieldInterface::PROP_TIMESTAMP],
            $builder,
            true
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForTimestamp(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            TimestampFieldInterface::PROP_TIMESTAMP,
            new DateTime()
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime|null $timestamp
     *
     * @return $this|TimestampFieldInterface
     */
    public function setTimestamp(?\DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;
        if ($this instanceof EntityInterface) {
            $this->validateProperty(TimestampFieldInterface::PROP_TIMESTAMP);
        }

        return $this;
    }
}
