<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

trait ReciprocatesTemplateEntity
{
    /**
     * This method needs to set the relationship on the templateEntity to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param TemplateEntity $templateEntity
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($templateEntity, $method)) {
            $method = 'set'.$singular;
        }

        $templateEntity->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntity $templateEntity
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $templateEntity->$method($this, false);

        return $this;
    }

}
