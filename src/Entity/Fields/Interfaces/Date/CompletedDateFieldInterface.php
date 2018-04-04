<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date;

interface CompletedDateFieldInterface
{
    public const PROP_COMPLETED_DATE = 'completedDate';

    public function getCompletedDate(): ?\DateTime;

    public function setCompletedDate(?\DateTime $completedDate): CompletedDateFieldInterface;
}
