<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person;

interface EmailFieldInterface
{
    public const PROP_EMAIL = 'email';

    public function getEmail(): ?string;

    public function setEmail(?string $email);
}
