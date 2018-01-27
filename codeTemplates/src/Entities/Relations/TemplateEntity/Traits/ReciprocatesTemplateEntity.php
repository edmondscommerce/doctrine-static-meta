<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

trait ReciprocatesTemplateEntity
{
    /**
     * This method needs to set the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntity $templateEntity
     *
     * @return $this||UsesPHPMetaData
     */
    public function reciprocateRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaData
    {
        $method = 'add' . static::getSingular();
        $templateEntity->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntity $templateEntity
     *
     * @return $this|UsesPHPMetaData
     */
    public function removeRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaData
    {
        $method = 'remove' . static::getSingular();
        $templateEntity->$method($this, false);

        return $this;
    }

}
