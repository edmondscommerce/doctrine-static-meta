<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
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
