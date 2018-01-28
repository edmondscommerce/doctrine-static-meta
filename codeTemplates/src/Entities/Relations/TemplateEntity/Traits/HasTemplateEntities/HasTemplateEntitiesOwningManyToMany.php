<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

trait HasTemplateEntitiesOwningManyToMany
{
    use HasTemplateEntitiesAbstract;

    public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            TemplateEntity::getPlural(), TemplateEntity::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . TemplateEntity::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            TemplateEntity::getSingular() . '_' . TemplateEntity::getIdField(),
            TemplateEntity::getIdField()
        );
        $builder->build();
    }
}
