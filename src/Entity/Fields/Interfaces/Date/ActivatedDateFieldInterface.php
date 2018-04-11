<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date;

interface ActivatedDateFieldInterface
{
    public const PROP_ACTIVATED_DATE = 'activatedDate';

    public function getActivatedDate(): ?\DateTime;

    public function setActivatedDate(?\DateTime $activatedDate);
}
