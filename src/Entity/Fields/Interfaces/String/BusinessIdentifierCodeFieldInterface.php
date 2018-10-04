<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface BusinessIdentifierCodeFieldInterface
{
    public const PROP_BUSINESS_IDENTIFIER_CODE = 'businessIdentifierCode';

    public const DEFAULT_BUSINESS_IDENTIFIER_CODE = null;

    public function getBusinessIdentifierCode(): ?string;

    public function setBusinessIdentifierCode(?string $businessIdentifierCode);
}
