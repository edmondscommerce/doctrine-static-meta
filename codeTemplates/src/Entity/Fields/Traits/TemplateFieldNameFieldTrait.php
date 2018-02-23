<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Traits;

use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Fields\Interfaces\TemplateFieldNameFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;


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
    public function setTemplateFieldName(string $templateFieldName): TemplateFieldNameFieldInterface
    {
        $this->templateFieldName = $templateFieldName;

        return $this;
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForTemplateFieldName(ValidatorClassMetaData $metadata): void
    {
        /**
         * Uncomment this block, replace NotBlank() with your choice of constraint
         *
         * @see vendor/symfony/validator/Constraints for Constraints to use
         * @see https://symfony.com/doc/current/validation.html#supported-constraints for docs
         *
        $metadata->addPropertyConstraint(
            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
            new NotBlank()
        );
         */
    }
}
