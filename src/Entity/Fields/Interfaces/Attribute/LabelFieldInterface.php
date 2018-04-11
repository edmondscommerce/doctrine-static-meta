<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface LabelFieldInterface
{
    public const PROP_NAME = 'label';

    public function getLabel(): ?string;

    public function setLabel(?string $label);
}
