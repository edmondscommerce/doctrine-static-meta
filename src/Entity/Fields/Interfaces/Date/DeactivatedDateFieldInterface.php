<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date;

interface DeactivatedDateFieldInterface
{
    public const PROP_DEACTIVATED_DATE = 'deactivatedDate';

    public function getDeactivatedDate(): ?\DateTime;

    public function setDeactivatedDate(?\DateTime $deactivatedDate);
}