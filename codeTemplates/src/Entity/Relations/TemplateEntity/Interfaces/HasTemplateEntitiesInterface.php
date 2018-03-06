<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity;

interface HasTemplateEntitiesInterface
{
    public const PROPERTY_NAME_TEMPLATE_ENTITIES = 'templateEntities';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForTemplateEntities(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|TemplateEntity[]
     */
    public function getTemplateEntities(): Collection;

    /**
     * @param Collection|TemplateEntity[] $templateEntities
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setTemplateEntities(Collection $templateEntities): UsesPHPMetaDataInterface;

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addTemplateEntities(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntities(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface;

}
