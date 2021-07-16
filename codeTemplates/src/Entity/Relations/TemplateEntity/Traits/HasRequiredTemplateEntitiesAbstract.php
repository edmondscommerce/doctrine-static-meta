<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasRequiredTemplateEntitiesInterface;
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
     * @var Collection<TemplateEntityInterface>
     */
    private Collection $templateEntities;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    public static function validatorMetaForPropertyTemplateEntities(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasRequiredTemplateEntitiesInterface::PROPERTY_NAME_TEMPLATE_ENTITIES,
            new Count(['min' => 1])
        );
        $metadata->addPropertyConstraint(
            HasRequiredTemplateEntitiesInterface::PROPERTY_NAME_TEMPLATE_ENTITIES,
            new NotBlank()
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

    private static function dsmInitRequiredRelationForTemplateEntity(DoctrineStaticMeta $dsm): void
    {
        $dsm->setRequiredRelationProperty(
            new DoctrineStaticMeta\RequiredRelation(
                HasRequiredTemplateEntitiesInterface::PROPERTY_NAME_TEMPLATE_ENTITIES,
                TemplateEntityInterface::class,
                true
            )
        );
    }

    /**
     * @return Collection<TemplateEntityInterface>
     */
    public function getTemplateEntities(): Collection
    {
        $return = new ArrayCollection();
        foreach ($this->templateEntities as $entity) {
            $return->add($entity);
        }

        return $return;
    }

    /**
     * @param Collection<TemplateEntityInterface> $templateEntities
     *
     * @return $this
     */
    public function setTemplateEntities(
        Collection $templateEntities
    ): static {
        foreach ($this->templateEntities as $templateEntity) {
            $this->removeTemplateEntity($templateEntity);
        }
        foreach ($templateEntities as $newTemplateEntity) {
            $this->addTemplateEntity($newTemplateEntity);
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
    ): static {
        $this->removeFromEntityCollectionAndNotify('templateEntities', $templateEntity);
        if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
            $this->removeRelationOnTemplateEntity(
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
    public function addTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): static {
        $this->addToEntityCollectionAndNotify('templateEntities', $templateEntity);
        if ($this instanceof ReciprocatesTemplateEntityInterface && true === $recip) {
            $this->reciprocateRelationOnTemplateEntity(
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
    private function initTemplateEntities(): static
    {
        if (isset($this->templateEntities)) {
            throw new \RuntimeException('Initialising entities that are already initialised in ' . __METHOD__);
        }
        $this->templateEntities = new ArrayCollection();

        return $this;
    }
}
