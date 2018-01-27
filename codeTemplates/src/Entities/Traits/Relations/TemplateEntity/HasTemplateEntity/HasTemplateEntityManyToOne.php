<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntityAbstract;

/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of TemplateEntity.
 *
 * TemplateEntity has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
trait HasTemplateEntityManyToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    protected static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class,
            static::getPlural()
        );
    }
}
