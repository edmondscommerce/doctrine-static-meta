<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

interface ReciprocatesTemplateEntity
{
    public function reciprocateRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaData;

    public function removeRelationOnTemplateEntity(TemplateEntity $templateEntity): UsesPHPMetaData;
}
