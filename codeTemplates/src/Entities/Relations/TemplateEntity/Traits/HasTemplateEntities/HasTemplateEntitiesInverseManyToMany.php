<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

trait HasTemplateEntitiesInverseManyToMany
{
    use HasTemplateEntitiesAbstract;

    protected static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {
        $builder->addInverseManyToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class,
            static::getPlural()
        );
    }
}
