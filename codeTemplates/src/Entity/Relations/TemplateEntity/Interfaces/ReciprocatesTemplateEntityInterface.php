<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

interface ReciprocatesTemplateEntityInterface
{
    /**
     * @param TemplateEntity|null $templateEntity
     *
     * @return self
     */
    public function reciprocateRelationOnTemplateEntity(
        TemplateEntity $templateEntity
    ): self;

    /**
     * @param TemplateEntity $templateEntity
     *
     * @return self
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntity $templateEntity
    ): self;
}
