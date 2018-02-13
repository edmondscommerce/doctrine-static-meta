<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entities\TemplateEntity;

/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
trait HasTemplateEntityUnidirectionalManyToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
