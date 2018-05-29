<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Fields\Interfaces\TemplateFieldNameFieldInterface;

trait TemplateFieldNameFieldTrait
{
    /**
     * @var string
     */
    private $templateFieldName;


    /**
     * This method sets the validation for this field.
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected static function validatorMetaForTemplateFieldName(ValidatorClassMetaData $metadata): void
    {
//        $metadata->addPropertyConstraint(
//            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
//            new NotBlank()
//        );
    }

    /**
     * @return string
     */
    public function getTemplateFieldName(): string
    {
        if (null === $this->templateFieldName) {
            return TemplateFieldNameFieldInterface::DEFAULT_TEMPLATE_FIELD_NAME;
        }

        return $this->templateFieldName;
    }

    /**
     * @param string $templateFieldName
     *
     * @return self
     */
    public function setTemplateFieldName(string $templateFieldName): self
    {
        $this->templateFieldName = $templateFieldName;

        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME);
        }

        return $this;
    }
}
