<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntityUnidirectionalOneToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
