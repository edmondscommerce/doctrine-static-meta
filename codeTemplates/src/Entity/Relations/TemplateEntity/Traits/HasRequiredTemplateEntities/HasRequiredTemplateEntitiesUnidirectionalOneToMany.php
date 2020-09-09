<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntities;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ReflectionException;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntitiesAbstract;

/**
 * Trait HasRequiredTemplateEntitiesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to TemplateEntity.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasRequiredTemplateEntities
 */
// phpcs:enable
trait HasRequiredTemplateEntitiesUnidirectionalOneToMany
{
    use HasRequiredTemplateEntitiesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntities(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getDoctrineStaticMeta()->getPlural(),
            TemplateEntity::class
        );
        $fromTableName     = MappingHelper::getInflector()->tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = MappingHelper::getInflector()->tableize(TemplateEntity::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            MappingHelper::getInflector()->tableize(self::getDoctrineStaticMeta()->getSingular()) . '_' . static::PROP_ID,
            static::PROP_ID,
            false
        );
        $manyToManyBuilder->addInverseJoinColumn(
            MappingHelper::getInflector()->tableize(
                TemplateEntity::getDoctrineStaticMeta()->getSingular()
            ) . '_' . TemplateEntity::PROP_ID,
            TemplateEntity::PROP_ID,
            false
        );
        $manyToManyBuilder->build();
    }
}
