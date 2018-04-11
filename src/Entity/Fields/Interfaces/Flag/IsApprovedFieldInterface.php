<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag;

interface IsApprovedFieldInterface
{
    public const PROP_IS_APPROVED = 'isApproved';

    public function getIsApproved(): int;

    public function setIsApproved(int $isApproved);
}
