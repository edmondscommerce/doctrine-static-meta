<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class EmailAddressFakerData extends AbstractFakerDataProvider
{
    public function __invoke(): string
    {
        return $this->generator->unique()->email;
    }
}
