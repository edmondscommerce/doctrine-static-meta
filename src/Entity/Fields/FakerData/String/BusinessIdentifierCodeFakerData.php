<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;

class BusinessIdentifierCodeFakerData extends AbstractFakerDataProvider
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

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
    }


    public function __invoke(): string
    {
        return $this->getBank() . $this->getCountryCode() . $this->getRegionAndBranch();
    }

    private function getBank(): string
    {
        return $this->generator->regexify('[A-Z]{4}');
    }

    private function getCountryCode(): string
    {
        //to prevent issues when using as an archetype, otherwise this gets replaced with the new field property name
        $property = 'country' . 'Code';
        do {
            $code = $this->generator->$property;
        } while (\in_array($code, self::EXCLUDED_CODES, true));

        return $code;
    }

    private function getRegionAndBranch(): string
    {
        return $this->generator->regexify('^([0-9A-Z]){2}([0-9A-Z]{3})?$');
    }
}
