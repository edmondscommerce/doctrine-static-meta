<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date;

interface TimestampFieldInterface
{
    public const PROP_TIMESTAMP = 'timestamp';

    public function getTimestamp(): ?\DateTime;

    public function setTimestamp(?\DateTime $timestamp);
}
