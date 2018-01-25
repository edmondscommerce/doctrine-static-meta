<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntitiesAbstract;

trait HasTemplateEntitiesManyToOne
{
    use HasTemplateEntitiesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    protected static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            TemplateEntity::getPlural(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}
