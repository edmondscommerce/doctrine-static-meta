<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\EntityRelations\TemplateEntity\Interfaces\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntitiesAbstract
{
    /**
     * @var ArrayCollection|TemplateEntity[]
     */
    private $templateEntities;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $manyToManyBuilder): void;

    /**
     * @return Collection|TemplateEntity[]
     */
    public function getTemplateEntities(): Collection
    {
        return $this->templateEntities;
    }

    /**
     * @param Collection $templateEntities
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setTemplateEntities(Collection $templateEntities): UsesPHPMetaDataInterface
    {
        $this->templateEntities = $templateEntities;

        return $this;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function addTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->templateEntities->contains($templateEntity)) {
            $this->templateEntities->add($templateEntity);
            if ($this instanceof ReciprocatesTemplateEntity && true === $recip) {
                $this->reciprocateRelationOnTemplateEntity($templateEntity);
            }
        }

        return $this;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->templateEntities->removeElement($templateEntity);
        if ($this instanceof ReciprocatesTemplateEntity && true === $recip) {
            $this->removeRelationOnTemplateEntity($templateEntity);
        }

        return $this;
    }

    /**
     * Initialise the templateEntities property as a Doctrine ArrayCollection
     *
     * @return $this
     */
    private function initTemplateEntities()
    {
        $this->templateEntities = new ArrayCollection();

        return $this;
    }
}
