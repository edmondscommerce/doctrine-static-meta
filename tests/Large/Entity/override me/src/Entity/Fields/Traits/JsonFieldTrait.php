<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\JsonFieldInterface;

// phpcs:enable
trait JsonFieldTrait {

	/**
	 * @var string|null
	 */
	private $json;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForJson(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleJsonFields(
		            [JsonFieldInterface::PROP_JSON],
		            $builder,
		            JsonFieldInterface::DEFAULT_JSON
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
	protected static function validatorMetaForJson(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            JsonFieldInterface::PROP_JSON,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getJson(): ?string {
		if (null === $this->json) {
		    return JsonFieldInterface::DEFAULT_JSON;
		}
		return $this->json;
	}

	/**
	 * @param string|null $json
	 * @return self
	 */
	public function setJson(?string $json): self {
		$this->updatePropertyValueThenValidateAndNotify(
            JsonFieldInterface::PROP_JSON,
             $json
        );
		return $this;
	}

	private function initJson() {
		$this->json = JsonFieldInterface::DEFAULT_JSON;
	}
}
