<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits;

use TemplateNamespace\Entities\TemplateEntity;

trait ReciprocatesTemplateEntity
{
    /**
     * This method needs to set the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntity $templateEntity
     *
     * @return $this
     */
    protected function reciprocateRelationOnTemplateEntity(TemplateEntity $templateEntity)
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
     * @return $this
     */
    protected function removeRelationOnTemplateEntity(TemplateEntity $templateEntity)
    {
        $method = 'remove' . static::getSingular();
        $templateEntity->$method($this, false);

        return $this;
    }

}
