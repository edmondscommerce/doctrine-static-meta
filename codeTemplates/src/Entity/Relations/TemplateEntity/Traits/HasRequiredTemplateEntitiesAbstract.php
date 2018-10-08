<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasTemplateEntitiesInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

/**
 * Trait HasTemplateEntitiesAbstract
 *
 * The base trait for relations to multiple TemplateEntities
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits
 */
// phpcs:enable
trait HasRequiredTemplateEntitiesAbstract
{
    /**
     * @var ArrayCollection|TemplateEntityInterface[]
     */
    private $templateEntities;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForTemplateEntities(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasTemplateEntitiesInterface::PROPERTY_NAME_TEMPLATE_ENTITIES,
            new Valid(),
            new Count(['min' => 1])
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
     * @return Collection|TemplateEntityInterface[]
     */
    public function getTemplateEntities(): Collection
    {
        return $this->templateEntities;
    }

    /**
     * @param Collection|TemplateEntityInterface[] $templateEntities
     *
     * @return $this
     */
    public function setTemplateEntities(
        Collection $templateEntities
    ): self {
        $this->setEntityCollectionAndNotify(
            'templateEntities',
            $templateEntities
        );

        return $this;
    }

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): self {
        $this->addToEntityCollectionAndNotify('templateEntities', $templateEntity);
        if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
            $this->reciprocateRelationOnTemplateEntity(
                $templateEntity
            );
        }

        return $this;
    }

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): self {
        $this->removeFromEntityCollectionAndNotify('templateEntities', $templateEntity);
        if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
            $this->removeRelationOnTemplateEntity(
                $templateEntity
            );
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
