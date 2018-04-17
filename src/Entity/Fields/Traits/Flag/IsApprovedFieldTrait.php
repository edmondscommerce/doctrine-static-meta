<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\IsApprovedFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait IsApprovedFieldTrait
{

    /**
     * @var bool
     */
    private $isApproved;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForIsApproved(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleBooleanFields(
            [IsApprovedFieldInterface::PROP_IS_APPROVED],
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
            IsApprovedFieldInterface::PROP_IS_APPROVED,
            new NotNull()
        );
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    /**
     * @param bool $isApproved
     *
     * @return $this|IsApprovedFieldInterface
     */
    public function setIsApproved(bool $isApproved)
    {
        $this->isApproved = $isApproved;
        if ($this instanceof EntityInterface) {
            $this->validateProperty(IsApprovedFieldInterface::PROP_IS_APPROVED);
        }

        return $this;
    }
}
