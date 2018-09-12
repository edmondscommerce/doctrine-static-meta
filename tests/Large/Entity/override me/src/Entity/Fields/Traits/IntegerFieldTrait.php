<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\IntegerFieldInterface;

// phpcs:enable
trait IntegerFieldTrait {

	/**
	 * @var int|null
	 */
	private $integer;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForInteger(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleIntegerFields(
		            [IntegerFieldInterface::PROP_INTEGER],
		            $builder,
		            IntegerFieldInterface::DEFAULT_INTEGER,
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
	 * @see https://symfony.com/doc/current/validation.html#supported-constraints
	 * @param ValidatorClassMetaData $metadata
	 * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
	 * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
	 * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
	 */
	protected static function validatorMetaForInteger(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            IntegerFieldInterface::PROP_INTEGER,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return int|null
	 */
	public function getInteger(): ?int {
		if (null === $this->integer) {
		    return IntegerFieldInterface::DEFAULT_INTEGER;
		}
		return $this->integer;
	}

	/**
	 * @param int|null $integer
	 * @return self
	 */
	public function setInteger(?int $integer): self {
		$this->updatePropertyValueThenValidateAndNotify(
            IntegerFieldInterface::PROP_INTEGER,
             $integer
        );
		return $this;
	}

	private function initInteger() {
		$this->integer = IntegerFieldInterface::DEFAULT_INTEGER;
	}
}
