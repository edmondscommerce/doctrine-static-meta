<?php declare(strict_types=1);
// phpcs:disable

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasRequiredTemplateEntityInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityInverseOneToOne
{
    use HasRequiredTemplateEntityAbstract;

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
        $inverseOneToOne = $builder->createOneToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class
        );
        $inverseOneToOne
            ->mappedBy(self::getDoctrineStaticMeta()->getSingular())
            ->addJoinColumn(
                Inflector::tableize(TemplateEntity::getDoctrineStaticMeta()->getSingular()) . '_' . IdFieldInterface::PROP_ID,
                 IdFieldInterface::PROP_ID,
                false
            )->build();
    }
}
