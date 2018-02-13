<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface ReciprocatesTemplateEntity
{
    public function reciprocateRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaDataInterface;

    public function removeRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaDataInterface;
}
