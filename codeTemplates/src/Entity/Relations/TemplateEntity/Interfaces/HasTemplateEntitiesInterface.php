<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

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
     * @return self
     */
    public function setTemplateEntities(Collection $templateEntities): self;

    /**
     * @param TemplateEntity|null $templateEntity
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addTemplateEntity(
        ?TemplateEntity $templateEntity,
        bool $recip = true
    ): self;

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        TemplateEntity $templateEntity,
        bool $recip = true
    ): self;

}
