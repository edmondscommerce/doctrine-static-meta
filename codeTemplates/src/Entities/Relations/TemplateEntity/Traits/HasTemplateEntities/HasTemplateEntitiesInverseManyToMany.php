<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;

trait HasTemplateEntitiesInverseManyToMany
{
    use HasTemplateEntitiesAbstract;

    use ReciprocatesTemplateEntity;

    public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder): void
    {
        $builder = $builder->createManyToMany(
            TemplateEntity::getPlural(), TemplateEntity::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(TemplateEntity::getPlural() . '_to_' . static::getPlural());
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
