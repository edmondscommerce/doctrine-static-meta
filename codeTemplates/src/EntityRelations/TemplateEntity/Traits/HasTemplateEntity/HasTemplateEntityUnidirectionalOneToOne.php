<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\EntityRelations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntityUnidirectionalOneToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
