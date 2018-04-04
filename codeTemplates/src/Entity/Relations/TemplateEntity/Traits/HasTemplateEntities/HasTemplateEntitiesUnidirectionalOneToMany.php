<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

/**
 * Trait HasTemplateEntitiesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to TemplateEntity.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntities
 */
trait HasTemplateEntitiesUnidirectionalOneToMany
{
    use HasTemplateEntitiesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForTemplateEntities(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getPlural(),
            TemplateEntity::class
        );
        $joinTableName = self::createJoinTableName(static::getSingular(), TemplateEntity::getPlural());
        $manyToManyBuilder->setJoinTable($joinTableName);
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            TemplateEntity::getSingular().'_'.TemplateEntity::getIdField(),
            TemplateEntity::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
