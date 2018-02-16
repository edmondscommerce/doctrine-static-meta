<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Fields;

interface LabelFieldInterface
{
    public const PROPERTY_NAME = 'label';

    public function getLabel(): string;

    public function setLabel(string $label): LabelFieldInterface;
}
