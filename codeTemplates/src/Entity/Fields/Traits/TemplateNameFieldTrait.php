<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Interfaces;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait TemplateNameFieldTrait
{
    /**
     * @var string
     */
    private $templateName;

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     *
     * @return $this|TemplateNameFieldInterface
     */
    public function setTemplateName(string $templateName): TemplateNameFieldInterface
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIpAddress(ValidatorClassMetaData $metadata): void
    {
        /**
         * Uncomment this block, replace NotBlank() with your choice of constraint
         *
         * @see vendor/symfony/validator/Constraints for Constraints to use
         * @see https://symfony.com/doc/current/validation.html#supported-constraints for docs
         *
        $metadata->addPropertyConstraint(
            TemplateNameFieldInterface::PROP_TEMPLATE_NAME,
            new NotBlank()
        );
         */
    }
}
