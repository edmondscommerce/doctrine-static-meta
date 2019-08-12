<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use function in_array;

class CountryCodeFakerData extends AbstractFakerDataProvider
{
    /**
     * @see https://github.com/symfony/symfony/issues/18263
     * @see \Symfony\Component\Intl\Data\Generator\RegionDataGenerator
     */
    public const EXCLUDED_CODES = [
        'ZZ',
        'BV',
        'QO',
        'EU',
        'AN',
        'BV',
        'HM',
        'CP',
    ];

    public function __invoke()
    {
        //to prevent issues when using as an archetype, otherwise this gets replaced with the new field property name
        $property = 'country' . 'Code';
        do {
            $code = $this->generator->$property;
        } while (in_array($code, self::EXCLUDED_CODES, true));

        return $code;
    }
}
