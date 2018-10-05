<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasRequiredRelationOnTemplateEntityInterface;

trait CanRequireTemplateEntity
{
    /**
     * Can the relation on TemplateEntity be null?
     *
     * We check if the current Entity is implementing the HasRequiredRelation interface.
     * If it is, then it can not be null
     *
     * @return bool
     */
    private static function canBeNullTemplateEntity(): bool
    {
        $interfaces = array_flip(static::getDoctrineStaticMeta()->getReflectionClass()->getInterfaceNames());

        return false === isset($interfaces[HasRequiredRelationOnTemplateEntityInterface::class]);
    }
}