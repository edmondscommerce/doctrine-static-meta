<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Attribute;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\TotalFieldInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait TotalFieldTrait {

	/**
	 * @var float
	 */
	private $total;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForTotal(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleFloatFields(
		            [TotalFieldInterface::PROP_TOTAL],
		            $builder,
		            false
		        );
	}

	/**
	 * This method sets the validation for this field.
	 *
	 * You should add in as many relevant property constraints as you see fit.
	 * 
	 * Remove the PHPMD suppressed warning once you start setting constraints
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @param ValidatorClassMetaData $metadata
	 * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
	 * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
	 * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
	 */
	protected static function getPropertyValidatorMetaForTotal(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            TotalFieldInterface::PROP_TOTAL,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return float
	 */
	public function getTotal(): float {
		return $this->total;
	}

	/**
	 * @param float $total
	 * @return self
	 */
	public function setTotal(float $total): self {
		$this->total = $total;
		if ($this instanceof EntityInterface) {
		    $this->validateProperty(TotalFieldInterface::PROP_TOTAL);
		}
		return $this;
	}
}
