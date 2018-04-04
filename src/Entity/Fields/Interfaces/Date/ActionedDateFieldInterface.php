<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date;

interface ActionedDateFieldInterface
{
    public const PROP_ACTIONED_DATE = 'actionedDate';

    public function getActionedDate(): ?\DateTime;

    public function setActionedDate(?\DateTime $actionedDate): ActionedDateFieldInterface;
}
