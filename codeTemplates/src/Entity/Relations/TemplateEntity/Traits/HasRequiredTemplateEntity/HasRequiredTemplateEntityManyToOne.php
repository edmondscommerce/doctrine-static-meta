<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ReflectionException;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasRequiredTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of TemplateEntity.
 *
 * TemplateEntity has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasRequiredTemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityManyToOne
{
    use HasRequiredTemplateEntityAbstract;

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
        $manyToOne = $builder->createManyToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class
        );
        $manyToOne
            ->inversedBy(self::getDoctrineStaticMeta()->getPlural())
            ->addJoinColumn(
                Inflector::tableize(
                    TemplateEntity::getDoctrineStaticMeta()->getSingular()
                ) . '_' . IdFieldInterface::PROP_ID,
                IdFieldInterface::PROP_ID,
                true
            )->build();

    }
}
