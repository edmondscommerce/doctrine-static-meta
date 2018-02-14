<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntity
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder): void;

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
