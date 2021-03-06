<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

interface HasTemplateEntitiesInterface
{
    public const PROPERTY_NAME_TEMPLATE_ENTITIES = 'templateEntities';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForTemplateEntities(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|TemplateEntityInterface[]
     */
    public function getTemplateEntities(): Collection;

    /**
     * @param Collection|TemplateEntityInterface[] $templateEntities
     *
     * @return $this
     */
    public function setTemplateEntities(Collection $templateEntities);

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    );

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    );

}
