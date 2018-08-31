<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

interface ReciprocatesTemplateEntityInterface
{
    /**
     * @param TemplateEntityInterface $templateEntity
     *
     * @return self
     */
    public function reciprocateRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): self;

    /**
     * @param TemplateEntityInterface $templateEntity
     *
     * @return self
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): self;
}
