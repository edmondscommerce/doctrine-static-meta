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
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
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
                MappingHelper::getInflector()->tableize(
                    TemplateEntity::getDoctrineStaticMeta()->getSingular()
                ) . '_' . IdFieldInterface::PROP_ID,
                IdFieldInterface::PROP_ID,
                /**
                 * We have had to make this a nullable column due to the fact that Doctrine will execute inserts
                 * sequentially and so the related entity ID may not yet exist in the database.
                 *
                 * @see \Doctrine\ORM\Persisters\Entity\BasicEntityPersister::prepareUpdateData
                 */
                true
            )->build();
    }
}
