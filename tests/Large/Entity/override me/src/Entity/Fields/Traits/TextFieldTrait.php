<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\TextFieldInterface;

// phpcs:enable
trait TextFieldTrait {

	/**
	 * @var string|null
	 */
	private $text;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForText(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleTextFields(
		            [TextFieldInterface::PROP_TEXT],
		            $builder,
		            TextFieldInterface::DEFAULT_TEXT
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
	protected static function validatorMetaForText(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            TextFieldInterface::PROP_TEXT,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getText(): ?string {
		if (null === $this->text) {
		    return TextFieldInterface::DEFAULT_TEXT;
		}
		return $this->text;
	}

	/**
	 * @param string|null $text
	 * @return self
	 */
	public function setText(?string $text): self {
		$this->updatePropertyValueThenValidateAndNotify(
            TextFieldInterface::PROP_TEXT,
             $text
        );
		return $this;
	}

	private function initText() {
		$this->text = TextFieldInterface::DEFAULT_TEXT;
	}
}
