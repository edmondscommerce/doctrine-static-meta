<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntityInterface
{
    public const PROPERTY_NAME_TEMPLATE_ENTITY = 'templateEntity';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForTemplateEntity(ClassMetadataBuilder $builder): void;

    /**
     * @return null|TemplateEntity
     */
    public function getTemplateEntity(): ?TemplateEntity;

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeTemplateEntity(): UsesPHPMetaDataInterface;

}