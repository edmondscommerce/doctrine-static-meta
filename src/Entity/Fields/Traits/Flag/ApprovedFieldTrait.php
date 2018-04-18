<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\ApprovedFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ApprovedFieldTrait
{

    /**
     * @var bool
     */
    private $approved;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function getPropertyDoctrineMetaForIsApproved(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleBooleanFields(
            [ApprovedFieldInterface::PROP_APPROVED],
            $builder,
            false
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIsApproved(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            ApprovedFieldInterface::PROP_APPROVED,
            new NotNull()
        );
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     *
     * @return $this|ApprovedFieldInterface
     */
    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(ApprovedFieldInterface::PROP_APPROVED);
        }

        return $this;
    }
}
