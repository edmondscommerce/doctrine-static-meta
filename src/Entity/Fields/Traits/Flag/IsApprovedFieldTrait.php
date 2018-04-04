<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\IsApprovedFieldInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait IsApprovedFieldTrait {

	/**
	 * @var int
	 */
	private $isApproved;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForIsApproved(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleIntegerFields(
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
    protected static function getPropertyValidatorMetaForIsDefault(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            IsApprovedFieldInterface::PROP_IS_APPROVED,
            new Range(0, 1)
        );

        $metadata->addPropertyConstraint(
            IsApprovedFieldInterface::PROP_IS_APPROVED,
            new NotNull()
        );
    }

	/**
	 * @return int
	 */
	public function getIsApproved(): int {
		return $this->isApproved;
	}

	/**
	 * @param int $isApproved
	 * @return $this|IsApprovedFieldInterface
	 */
	public function setIsApproved(int $isApproved) {
		$this->isApproved = $isApproved;
		return $this;
	}
}
