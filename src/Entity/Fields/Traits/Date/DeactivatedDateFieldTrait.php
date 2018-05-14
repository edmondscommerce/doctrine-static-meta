<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\DeactivatedDateFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DeactivatedDateFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $deactivatedDate;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDeactivatedDate(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE],
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
    protected static function getPropertyValidatorMetaForDeactivatedDate(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE,
            new DateTime()
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getDeactivatedDate(): ?\DateTime
    {
        return $this->deactivatedDate;
    }

    /**
     * @param \DateTime|null $deactivatedDate
     *
     * @return $this|DeactivatedDateFieldInterface
     */
    public function setDeactivatedDate(?\DateTime $deactivatedDate): self
    {
        $this->deactivatedDate = $deactivatedDate;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE);
        }

        return $this;
    }
}
