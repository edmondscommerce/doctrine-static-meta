<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\CompletedDateFieldInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait CompletedDateFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $completedDate;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCompletedDate(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleDatetimeFields(
            [CompletedDateFieldInterface::PROP_COMPLETED_DATE],
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
    protected static function getPropertyValidatorMetaForCompletedDate(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            CompletedDateFieldInterface::PROP_COMPLETED_DATE,
            new DateTime()
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedDate(): ?\DateTime
    {
        return $this->completedDate;
    }

    /**
     * @param \DateTime|null $completedDate
     * @return $this|CompletedDateFieldInterface
     */
    public function setCompletedDate(?\DateTime $completedDate)
    {
        $this->completedDate = $completedDate;
        return $this;
    }
}
