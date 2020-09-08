<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntities;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ReflectionException;
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
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = MappingHelper::getInflector()->tableize(TemplateEntity::getDoctrineStaticMeta()->getPlural());
        $toTableName   = MappingHelper::getInflector()->tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            MappingHelper::getInflector()->tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            MappingHelper::getInflector()->tableize(
                TemplateEntity::getDoctrineStaticMeta()->getSingular() . '_' . TemplateEntity::PROP_ID
            ),
            TemplateEntity::PROP_ID
        );
        $manyToManyBuilder->fetchExtraLazy();
        $manyToManyBuilder->build();
    }
}
