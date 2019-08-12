<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ReflectionException;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of TemplateEntity.
 *
 * TemplateEntity has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
// phpcs:enable
trait HasTemplateEntityManyToOne
{
    use HasTemplateEntityAbstract;

    use ReciprocatesTemplateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
