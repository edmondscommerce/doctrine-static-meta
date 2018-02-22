<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Interfaces;

interface TemplateFieldNameFieldInterface
{
    public const PROP_TEMPLATE_FIELD_NAME = 'templateFieldName';

    public function getTemplateFieldName(): string;

    public function setTemplateFieldName(string $templateFieldName): TemplateFieldNameFieldInterface;
}
