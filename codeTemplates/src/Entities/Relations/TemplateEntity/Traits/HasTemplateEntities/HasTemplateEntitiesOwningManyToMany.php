<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

trait HasTemplateEntitiesOwningManyToMany
{
    use HasTemplateEntitiesAbstract;

    protected static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {
        $builder->addOwningManyToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class,
            static::getPlural()
        );
    }
}
