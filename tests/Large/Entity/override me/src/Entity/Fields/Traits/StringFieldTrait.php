<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\StringFieldInterface;

// phpcs:enable
trait StringFieldTrait {

	/**
	 * @var string|null
	 */
	private $string;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForString(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleStringFields(
		            [StringFieldInterface::PROP_STRING],
		            $builder,
		            StringFieldInterface::DEFAULT_STRING,
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
	protected static function validatorMetaForString(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            StringFieldInterface::PROP_STRING,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getString(): ?string {
		if (null === $this->string) {
		    return StringFieldInterface::DEFAULT_STRING;
		}
		return $this->string;
	}

	/**
	 * @param string|null $string
	 * @return self
	 */
	public function setString(?string $string): self {
		$this->updatePropertyValueThenValidateAndNotify(
            StringFieldInterface::PROP_STRING,
             $string
        );
		return $this;
	}

	private function initString() {
		$this->string = StringFieldInterface::DEFAULT_STRING;
	}
}
