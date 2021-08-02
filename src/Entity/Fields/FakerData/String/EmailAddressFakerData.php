<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class EmailAddressFakerData extends AbstractFakerDataProvider
{
    public function __invoke(): string
    {
        /** @phpstan-ignore-next-line  - says method doesn't exist but it does */
        return $this->generator->unique()->email;
    }
}
