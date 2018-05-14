<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;



/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of TemplateEntity
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
// phpcs:enable
trait HasTemplateEntityUnidirectionalManyToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            TemplateEntity::getSingular(),
            TemplateEntity::class
        );
    }
}
