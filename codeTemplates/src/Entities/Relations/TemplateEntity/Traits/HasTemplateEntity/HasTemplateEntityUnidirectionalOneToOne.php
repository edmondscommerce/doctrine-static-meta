<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntity;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;

trait HasTemplateEntityUnidirectionalOneToOne
{
    use HasTemplateEntityAbstract;

    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
