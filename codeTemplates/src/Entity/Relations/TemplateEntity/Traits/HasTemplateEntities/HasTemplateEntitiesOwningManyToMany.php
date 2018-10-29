<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasTemplateEntitiesOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities
 */
// phpcs:enable
trait HasTemplateEntitiesOwningManyToMany
{
    use HasTemplateEntitiesAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntities(
        ClassMetadataBuilder $builder
    ): void {

        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getDoctrineStaticMeta()->getPlural(),
            TemplateEntity::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(TemplateEntity::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                TemplateEntity::getDoctrineStaticMeta()->getSingular() . '_' . TemplateEntity::PROP_ID
            ),
            TemplateEntity::PROP_ID
        );
        $manyToManyBuilder->fetchExtraLazy();
        $manyToManyBuilder->build();
    }
}
