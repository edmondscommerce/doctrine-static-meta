<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ReflectionException;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntityAbstract;
use TemplateNamespace\Entity\Relations\TemplateEntity\Traits\ReciprocatesTemplateEntity;

/**
 * Trait HasRequiredTemplateEntityOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and TemplateEntity
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits\HasRequiredTemplateEntity
 */
// phpcs:enable
trait HasRequiredTemplateEntityOwningOneToOne
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
        $owningOneToOne = $builder->createOneToOne(
            TemplateEntity::getDoctrineStaticMeta()->getSingular(),
            TemplateEntity::class);
        $owningOneToOne
            ->inversedBy(
                self::getDoctrineStaticMeta()->getSingular()
            )->addJoinColumn(
                MappingHelper::getInflector()->tableize(
                    TemplateEntity::getDoctrineStaticMeta()->getSingular()
                ) . '_' . IdFieldInterface::PROP_ID,
                IdFieldInterface::PROP_ID,

                true
            )->build();
    }
}
