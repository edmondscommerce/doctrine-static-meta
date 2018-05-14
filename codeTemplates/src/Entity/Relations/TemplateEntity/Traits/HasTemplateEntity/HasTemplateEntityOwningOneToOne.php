<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasTemplateEntityOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and TemplateEntity
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity
 */
// phpcs:enable
trait HasTemplateEntityOwningOneToOne
{
    use HasTemplateEntityAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOwningOneToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class,
            static::getSingular()
        );
    }
}
