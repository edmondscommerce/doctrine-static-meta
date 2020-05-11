<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime;

use DateTimeImmutable;

interface DateTimeRequiredFieldInterface
{
    public const PROP_DATE_TIME_REQUIRED = 'dateTimeRequired';

    public const DEFAULT_DATE_TIME_REQUIRED = null;

    public const DEFAULT_DATE_TIME_REQUIRED_DATE_STRING = '1970-01-01';


    public function getDateTimeRequired(): DateTimeImmutable;
}
