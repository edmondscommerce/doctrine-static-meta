<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntities
{
    public static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder);

    public function getTemplateEntities(): Collection;

    public function setTemplateEntities(Collection $templateEntities): UsesPHPMetaDataInterface;

    public function addTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

}
