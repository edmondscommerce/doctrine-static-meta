<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Interfaces;

interface TemplateFieldNameFieldInterface
{
    public const PROP_TEMPLATE_FIELD_NAME = 'templateFieldName';

    public const DEFAULT_TEMPLATE_FIELD_NAME = 'defaultValue';

    /**
     * @return string
     */
    public function getTemplateFieldName(): string;
}
