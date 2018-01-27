<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntitiesAbstract
{
    use ReciprocatesTemplateEntity;

    /**
     * @var ArrayCollection|TemplateEntity[]
     */
    private $templateEntities;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract protected static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder);

    /**
     * @return ArrayCollection|TemplateEntity[]
     */
    public function getTemplateEntities(): ArrayCollection
    {
        return $this->templateEntities;
    }

    /**
     * @param ArrayCollection $templateEntities
     *
     * @return $this|UsesPHPMetaData
     */
    public function setTemplateEntities(ArrayCollection $templateEntities): UsesPHPMetaData
    {
        $this->templateEntities = $templateEntities;

        return $this;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaData
     */
    public function addTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaData
    {
        if (!$this->templateEntities->contains($templateEntity)) {
            $this->templateEntities->add($templateEntity);
            if (true === $recip) {
                $this->reciprocateRelationOnTemplateEntity($templateEntity, false);
            }
        }

        return $this;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaData
     */
    public function removeTemplateEntity(TemplateEntity $templateEntity, bool $recip = true):UsesPHPMetaData
    {
        $this->templateEntities->removeElement($templateEntity);
        if (true === $recip) {
            $this->removeRelationOnTemplateEntity($templateEntity, false);
        }

        return $this;
    }

    private function initTemplateEntities()
    {
        $this->templateEntities = new ArrayCollection();

        return $this;
    }
}
