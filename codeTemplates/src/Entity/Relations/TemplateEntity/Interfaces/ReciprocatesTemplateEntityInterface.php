<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

interface ReciprocatesTemplateEntityInterface
{
    /**
     * @param TemplateEntityInterface $templateEntity
     *
     * @return $this
     */
    public function reciprocateRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    );

    /**
     * @param TemplateEntityInterface $templateEntity
     *
     * @return $this
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    );
}
