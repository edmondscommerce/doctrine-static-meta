<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Fields\Interfaces\TemplateFieldNameFieldInterface;

trait TemplateFieldNameFieldTrait
{
    /**
     * @var string
     */
    private $templateFieldName;

    /**
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateFieldName(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME],
            $builder,
            TemplateFieldNameFieldInterface::DEFAULT_TEMPLATE_FIELD_NAME
        );
    }

    /**
     * This method sets the validation for this field.
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     */
    protected static function validatorMetaForPropertyTemplateFieldName(ValidatorClassMetaData $metadata): void
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

    private function initTemplateFieldName(): void
    {
        $this->templateFieldName = TemplateFieldNameFieldInterface::DEFAULT_TEMPLATE_FIELD_NAME;
    }

    /**
     * @param string $templateFieldName
     *
     * @return self
     */
    private function setTemplateFieldName(string $templateFieldName): self
    {
        $this->updatePropertyValue(
            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
            $templateFieldName
        );

        return $this;
    }
}
