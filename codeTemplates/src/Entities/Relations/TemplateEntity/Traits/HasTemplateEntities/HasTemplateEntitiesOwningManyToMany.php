<?php declare(strict_types=1);


namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntities;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntitiesAbstract;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntitiesOwningManyToMany
{
    use HasTemplateEntitiesAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder): void
    {

        $manyToManyBuilder = $builder->createManyToMany(
            TemplateEntity::getPlural(), TemplateEntity::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.TemplateEntity::getPlural());
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
