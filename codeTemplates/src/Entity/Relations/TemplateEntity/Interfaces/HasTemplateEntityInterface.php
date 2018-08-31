<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

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
     * @return null|TemplateEntityInterface
     */
    public function getTemplateEntity(): ?TemplateEntityInterface;

    /**
     * @param TemplateEntityInterface|null $templateEntity
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        ?TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): HasTemplateEntityInterface;

    /**
     * @param null|TemplateEntityInterface $templateEntity
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        ?TemplateEntityInterface $templateEntity = null,
        bool $recip = true
    ): HasTemplateEntityInterface;

}
