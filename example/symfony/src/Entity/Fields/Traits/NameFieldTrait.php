<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use My\Test\Project\Entity\Fields\Interfaces\NameFieldInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait NameFieldTrait {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForName(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleStringFields(
		            [NameFieldInterface::PROP_NAME],
		            $builder,
		            true
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
	protected static function getPropertyValidatorMetaForName(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            NameFieldInterface::PROP_NAME,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string|null $name
	 * @return self
	 */
	public function setName(?string $name): self {
		$this->name = $name;
		if ($this instanceof ValidatedEntityInterface) {
		    $this->validateProperty(NameFieldInterface::PROP_NAME);
		}
		return $this;
	}
}
