<?php declare(strict_types=1);


namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;

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
    public static function getPropertyDoctrineMetaForTemplateEntities(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getPlural(), TemplateEntity::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(TemplateEntity::getPlural().'_to_'.static::getPlural());
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
