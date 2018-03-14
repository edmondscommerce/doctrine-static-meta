<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person;

interface NameFieldInterface
{
    public const PROPERTY_NAME = 'name';

    public function getName(): string;

    public function setName(string $name): NameFieldInterface;
}
