<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;

/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
trait HasTemplateEntityUnidirectionalManyToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    protected static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
