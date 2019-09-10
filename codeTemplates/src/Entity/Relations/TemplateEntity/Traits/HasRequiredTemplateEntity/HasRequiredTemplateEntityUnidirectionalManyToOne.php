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


/**
 * Trait HasRequiredTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasRequiredTemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityUnidirectionalManyToOne
{
    use HasRequiredTemplateEntityAbstract;

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
        $unidirectionalManyToOne = $builder->createManyToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class
        );
        $unidirectionalManyToOne->addJoinColumn(
            Inflector::tableize(
                TemplateEntity::getDoctrineStaticMeta()->getSingular()
            ) . '_' . IdFieldInterface::PROP_ID,
            IdFieldInterface::PROP_ID,
            true
        )->build();
    }
}
