<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

interface HasRequiredTemplateEntityInterface
{
    public const PROPERTY_NAME_TEMPLATE_ENTITY = 'templateEntity';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForTemplateEntity(ClassMetadataBuilder $builder): void;

    /**
     * @return TemplateEntityInterface
     */
    public function getTemplateEntity(): TemplateEntityInterface;

    /**
     * @param TemplateEntityInterface $templateEntity
     * @param bool                    $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        TemplateEntityInterface $templateEntity,
        bool $recip = true
    );
}
