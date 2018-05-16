<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag;

interface ApprovedFieldInterface
{
    public const PROP_APPROVED = 'approved';

    public const DEFAULT_APPROVED = false;

    public function isApproved(): bool;

    public function setApproved(bool $approved);
}
