<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface NameFieldInterface
{
    public const PROP_NAME = 'name';

    public function getName(): ?string;

    public function setName(?string $name): NameFieldInterface;
}
