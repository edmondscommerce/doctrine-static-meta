<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Attribute;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\PriceFieldInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait PriceFieldTrait {

	/**
	 * @var float
	 */
	private $price;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForPrice(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleFloatFields(
		            [PriceFieldInterface::PROP_PRICE],
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
	protected static function getPropertyValidatorMetaForPrice(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            PriceFieldInterface::PROP_PRICE,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return float
	 */
	public function getPrice(): float {
		return $this->price;
	}

	/**
	 * @param float $price
	 * @return self
	 */
	public function setPrice(float $price): self {
		$this->price = $price;
		if ($this instanceof ValidatedEntityInterface) {
		    $this->validateProperty(PriceFieldInterface::PROP_PRICE);
		}
		return $this;
	}
}
