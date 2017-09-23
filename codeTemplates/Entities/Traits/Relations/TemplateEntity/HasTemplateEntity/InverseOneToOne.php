<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntityAbstract;

trait InverseOneToOne
{
    use HasTemplateEntityAbstract;

    protected static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}