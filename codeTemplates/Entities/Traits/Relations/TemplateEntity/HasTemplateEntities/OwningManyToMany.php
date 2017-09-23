<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntitiesAbstract;

class OwningManyToMany
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