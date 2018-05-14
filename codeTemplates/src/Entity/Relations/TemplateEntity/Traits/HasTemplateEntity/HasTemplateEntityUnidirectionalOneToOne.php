<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

trait HasTemplateEntityUnidirectionalOneToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
