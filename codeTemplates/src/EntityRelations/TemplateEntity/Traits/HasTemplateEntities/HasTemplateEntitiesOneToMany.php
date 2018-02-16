<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;

/**
 * Trait HasTemplateEntitiesOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to TemplateEntity.
 *
 * The TemplateEntity has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities
 */
trait HasTemplateEntitiesOneToMany
{
    use HasTemplateEntitiesAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForTemplateEntities(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}
