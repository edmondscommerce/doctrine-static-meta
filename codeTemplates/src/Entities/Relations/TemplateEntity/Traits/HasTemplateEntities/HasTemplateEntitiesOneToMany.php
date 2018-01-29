<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

/**
 * Trait HasTemplateEntitiesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to TemplateEntity.
 *
 * The TemplateEntity has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities
 */
trait HasTemplateEntitiesOneToMany
{
    use HasTemplateEntitiesAbstract;

    public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}
