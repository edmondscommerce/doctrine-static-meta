<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class LocaleIdentifierFakerDataProvider extends AbstractFakerDataProvider
{
    public const EXCLUDED_LOCALES = [
        'trv_TW',
    ];

    public function __invoke(): string
    {
        do {
            $return = $this->generator->locale;
        } while (\in_array($return, self::EXCLUDED_LOCALES, true));

        return $return;
    }
}
