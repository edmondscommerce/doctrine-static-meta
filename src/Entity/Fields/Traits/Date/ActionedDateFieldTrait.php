<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActionedDateFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ActionedDateFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $actionedDate;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function getPropertyDoctrineMetaForActionedDate(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [ActionedDateFieldInterface::PROP_ACTIONED_DATE],
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
    protected static function getPropertyValidatorMetaForActionedDate(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            ActionedDateFieldInterface::PROP_ACTIONED_DATE,
            new DateTime()
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getActionedDate(): ?\DateTime
    {
        return $this->actionedDate;
    }

    /**
     * @param \DateTime|null $actionedDate
     *
     * @return $this|ActionedDateFieldInterface
     */
    public function setActionedDate(?\DateTime $actionedDate): self
    {
        $this->actionedDate = $actionedDate;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(ActionedDateFieldInterface::PROP_ACTIONED_DATE);
        }

        return $this;
    }
}
