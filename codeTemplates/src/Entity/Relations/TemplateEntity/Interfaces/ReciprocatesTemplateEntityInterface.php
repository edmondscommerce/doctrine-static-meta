<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

interface ReciprocatesTemplateEntityInterface
{
    /**
     * @param TemplateEntity $templateEntity
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnTemplateEntity(
        ?TemplateEntity $templateEntity
    ): UsesPHPMetaDataInterface;

    /**
     * @param TemplateEntity $templateEntity
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntity $templateEntity
    ): UsesPHPMetaDataInterface;
}
