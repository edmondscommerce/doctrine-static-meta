<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasTemplateEntitiesInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities
 */
// phpcs:enable
trait HasTemplateEntitiesInverseManyToMany
{
    use HasTemplateEntitiesAbstract;

    use ReciprocatesTemplateEntity;

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
            TemplateEntity::getDoctrineStaticMeta()->getPlural(), TemplateEntity::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(TemplateEntity::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular().'_'.static::getIdField()),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                TemplateEntity::getDoctrineStaticMeta()->getSingular().'_'.TemplateEntity::getIdField()
            ),
            TemplateEntity::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
