<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasTemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

/**
 * The base trait for required relations to a single TemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityAbstract
{
    /**
     * @var TemplateEntity
     */
    private $templateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForPropertyTemplateEntity(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            new NotBlank()
        );
        $metadata->addPropertyConstraint(
            HasTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            new Valid()
        );
    }

    /**
     * @return TemplateEntityInterface
     */
    public function getTemplateEntity(): TemplateEntityInterface
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): self {

        $this->setEntityAndNotify('templateEntity', $templateEntity);
        if (
            $this instanceof ReciprocatesTemplateEntityInterface
            && true === $recip
        ) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }

        return $this;
    }
}
