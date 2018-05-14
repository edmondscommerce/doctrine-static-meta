<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasTemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;


trait HasTemplateEntityAbstract
{
    /**
     * @var TemplateEntity|null
     */
    private $templateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForTemplateEntity(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForTemplateEntity(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            new Valid()
        );
    }

    /**
     * @return TemplateEntity|null
     */
    public function getTemplateEntity(): ?TemplateEntity
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntity|null $templateEntity
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        ?TemplateEntity $templateEntity,
        bool $recip = true
    ): HasTemplateEntityInterface {

        $this->templateEntity = $templateEntity;
        if (
            $this instanceof ReciprocatesTemplateEntityInterface
            && true === $recip
            && null !== $templateEntity
        ) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function removeTemplateEntity(): HasTemplateEntityInterface
    {
        $this->templateEntity = null;

        return $this;
    }
}
