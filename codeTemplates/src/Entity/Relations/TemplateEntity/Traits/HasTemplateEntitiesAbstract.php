<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasTemplateEntitiesInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

trait HasTemplateEntitiesAbstract
{
    /**
     * @var ArrayCollection|TemplateEntity[]
     */
    private $templateEntities;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForTemplateEntities(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasTemplateEntitiesInterface::PROPERTY_NAME_TEMPLATE_ENTITIES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForTemplateEntities(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|TemplateEntity[]
     */
    public function getTemplateEntities(): Collection
    {
        return $this->templateEntities;
    }

    /**
     * @param Collection|TemplateEntity[] $templateEntities
     *
     * @return self
     */
    public function setTemplateEntities(Collection $templateEntities): HasTemplateEntitiesInterface
    {
        $this->templateEntities = $templateEntities;

        return $this;
    }

    /**
     * @param TemplateEntity|null $templateEntity
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addTemplateEntity(
        ?TemplateEntity $templateEntity,
        bool $recip = true
    ): HasTemplateEntitiesInterface {
        if($templateEntity === null) {
            return $this;
        }

        if (!$this->templateEntities->contains($templateEntity)) {
            $this->templateEntities->add($templateEntity);
            if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
                $this->reciprocateRelationOnTemplateEntity($templateEntity);
            }
        }

        return $this;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        TemplateEntity $templateEntity,
        bool $recip = true
    ): HasTemplateEntitiesInterface {
        $this->templateEntities->removeElement($templateEntity);
        if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
            $this->removeRelationOnTemplateEntity($templateEntity);
        }

        return $this;
    }

    /**
     * Initialise the templateEntities property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initTemplateEntities()
    {
        $this->templateEntities = new ArrayCollection();

        return $this;
    }
}
