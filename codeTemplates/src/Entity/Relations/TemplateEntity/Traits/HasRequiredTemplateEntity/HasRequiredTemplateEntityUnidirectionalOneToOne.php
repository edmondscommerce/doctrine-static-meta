<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntityAbstract;

/**
 * Trait HasRequiredTemplateEntityUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityUnidirectionalOneToOne
{
    use HasRequiredTemplateEntityAbstract;

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
        $unidirectionalOneToOn = $builder->createOneToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class
        );
        $unidirectionalOneToOn->addJoinColumn(
            Inflector::tableize(TemplateEntity::getDoctrineStaticMeta()->getSingular()) . '_' . IdFieldInterface::PROP_ID,
            IdFieldInterface::PROP_ID,
            false
        )->build();
    }
}
