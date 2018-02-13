<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntity
{
    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder);

    public function getTemplateEntity(): ?TemplateEntity;

    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeTemplateEntity(): UsesPHPMetaDataInterface;

}
