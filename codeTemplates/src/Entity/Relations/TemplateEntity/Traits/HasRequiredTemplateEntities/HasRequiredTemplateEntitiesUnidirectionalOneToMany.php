<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntities;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntities(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getDoctrineStaticMeta()->getPlural(),
            TemplateEntity::class
        );
        $fromTableName     = Inflector::tableize(self::getDoctrineStaticMeta()->getSingular());
        $toTableName       = Inflector::tableize(TemplateEntity::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID,
            static::PROP_ID,
            false
        );
        $manyToManyBuilder->addInverseJoinColumn(
            TemplateEntity::getDoctrineStaticMeta()->getSingular() . '_' . TemplateEntity::PROP_ID,
            TemplateEntity::PROP_ID,
            false
        );
        $manyToManyBuilder->build();
    }
}
