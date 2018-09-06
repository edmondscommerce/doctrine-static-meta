<?php declare(strict_types=1);
// phpcs:disable

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasTemplateEntityInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and TemplateEntity
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity
 */
// phpcs:enable
trait HasTemplateEntityInverseOneToOne
{
    use HasTemplateEntityAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addInverseOneToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
