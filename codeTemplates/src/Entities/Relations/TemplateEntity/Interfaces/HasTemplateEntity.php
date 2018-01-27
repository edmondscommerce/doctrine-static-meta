<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntity
{
    static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder);

    public function getTemplateEntity(): ?TemplateEntity;

    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaData;

    public function removeTemplateEntity(): UsesPHPMetaData;

}
