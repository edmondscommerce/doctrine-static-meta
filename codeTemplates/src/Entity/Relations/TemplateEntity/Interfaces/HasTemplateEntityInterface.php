<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;

interface HasTemplateEntityInterface
{
    public const PROPERTY_NAME_TEMPLATE_ENTITY = 'templateEntity';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForTemplateEntity(ClassMetadataBuilder $builder): void;

    /**
     * @return null|TemplateEntity
     */
    public function getTemplateEntity(): ?TemplateEntity;

    /**
     * @param TemplateEntity|null $templateEntity
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        ?TemplateEntity $templateEntity,
        bool $recip = true
    ): HasTemplateEntityInterface;

    /**
     * @param bool $recip
     *
     * @return self
     */
    public function removeTemplateEntity(bool $recip): HasTemplateEntityInterface;

}
