<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

/**
 * Trait HasTemplateEntitiesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to TemplateEntity.
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities
 */
trait HasTemplateEntitiesUnidirectionalOneToMany
{
    use HasTemplateEntitiesAbstract;

    protected static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class
        );
    }
}
