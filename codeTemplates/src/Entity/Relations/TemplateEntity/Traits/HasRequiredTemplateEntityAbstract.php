<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasRequiredTemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

/**
 * The base trait for required relations to a single TemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityAbstract
{
    /**
     * @var TemplateEntityInterface
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
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    public static function validatorMetaForPropertyTemplateEntity(
        ValidatorClassMetaData $metadata
    ): void {
        $validConstraint = new Valid();
        $validConstraint->traverse = false;
        $metadata->addPropertyConstraint(
            HasRequiredTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            new NotBlank()
        );
        $metadata->addPropertyConstraint(
            HasRequiredTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            $validConstraint
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
