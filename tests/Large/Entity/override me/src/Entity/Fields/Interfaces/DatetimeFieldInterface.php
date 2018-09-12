<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface DatetimeFieldInterface
{
    public const PROP_DATETIME = 'datetime';

    public const DEFAULT_DATETIME = null;

    public function getDatetime(): ?\DateTime;

    public function setDatetime(?\DateTime $datetime);
}
