<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActivatedDateFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ActivatedDateFieldTrait
{

    /**
     * @var \DateTime|null
     */
    private $activatedDate;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForActivatedDate(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [ActivatedDateFieldInterface::PROP_ACTIVATED_DATE],
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
    protected static function validatorMetaForActivatedDate(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            ActivatedDateFieldInterface::PROP_ACTIVATED_DATE,
            new DateTime()
        );
    }

    /**
     * @return \DateTime|null
     */
    public function getActivatedDate(): ?\DateTime
    {
        return $this->activatedDate;
    }

    /**
     * @param \DateTime|null $activatedDate
     *
     * @return $this|ActivatedDateFieldInterface
     */
    public function setActivatedDate(?\DateTime $activatedDate): self
    {
        $this->activatedDate = $activatedDate;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(ActivatedDateFieldInterface::PROP_ACTIVATED_DATE);
        }

        return $this;
    }
}
