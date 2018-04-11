<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Fields\Interfaces\TemplateFieldNameFieldInterface;

trait TemplateFieldNameFieldTrait
{
    /**
     * @var string
     */
    private $templateFieldName;

    /**
     * @return string
     */
    public function getTemplateFieldName(): string
    {
        return $this->templateFieldName;
    }

    /**
     * @param string $templateFieldName
     *
     * @return $this|TemplateFieldNameFieldInterface
     */
    public function setTemplateFieldName(string $templateFieldName)
    {
        $this->templateFieldName = $templateFieldName;

        if ($this instanceof ValidateInterface) {
            $this->setNeedsValidating();
        }

        return $this;
    }

    /**
     * Uncomment this method, replace NotBlank() with your choice of constraint
     *
     * @see vendor/symfony/validator/Constraints for Constraints to use
     * @see https://symfony.com/doc/current/validation.html#supported-constraints for docs
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     *
     * protected static function getPropertyValidatorMetaForTemplateFieldName(ValidatorClassMetaData $metadata): void
     * {
     * $metadata->addPropertyConstraint(
     * TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
     * new NotBlank()
     * );
     *
     * }
     **/
}
