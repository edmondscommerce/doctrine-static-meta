<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class CountryCodeFakerData extends AbstractFakerDataProvider
{
    /**
     * @see https://github.com/symfony/symfony/issues/18263
     */
    public const EXCLUDED_COUNTRY_CODES = ['HM', 'BV'];

    public function __invoke()
    {
        do {
            $code = $this->generator->countryCode;
        } while (\in_array($code, self::EXCLUDED_COUNTRY_CODES, true));

        return $code;
    }
}
