<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface DomainNameFieldInterface
{
    public const PROP_DOMAIN_NAME = 'domainName';

    public const DEFAULT_DOMAIN_NAME = null;

    public function getDomainName(): ?string;
}
