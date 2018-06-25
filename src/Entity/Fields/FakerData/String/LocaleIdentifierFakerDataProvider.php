<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class LocaleIdentifierFakerDataProvider extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return $this->generator->locale;
    }
}
