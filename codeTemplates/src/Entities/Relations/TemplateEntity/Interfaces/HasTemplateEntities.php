<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntities
{
    static function getPropertyMetaForTemplateEntities(ClassMetadataBuilder $builder);

    public function getTemplateEntities(): ArrayCollection;

    public function setTemplateEntities(ArrayCollection $templateEntities): UsesPHPMetaData;

    public function addTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaData;

    public function removeTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaData;

}
