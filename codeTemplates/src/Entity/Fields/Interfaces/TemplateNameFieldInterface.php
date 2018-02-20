<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Fields\Interfaces;

interface TemplateNameFieldInterface
{
    public const PROP_TEMPLATE_NAME = 'templateName';

    public function getTemplateName(): string;

    public function setTemplateName(string $templateName): TemplateNameFieldInterface;
}
