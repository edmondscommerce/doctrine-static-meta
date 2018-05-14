<?php declare(strict_types=1);


namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;

trait HasTemplateEntityInverseOneToOne
{
    use HasTemplateEntityAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}
