<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;

class BusinessIdentifierCodeFakerData extends AbstractFakerDataProvider
{
    /**
     * @var CountryCodeFakerData
     */
    private $countryCode;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->countryCode = new CountryCodeFakerData($generator);
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
        return $this->countryCode->__invoke();
    }

    private function getRegionAndBranch(): string
    {
        return $this->generator->regexify('^([0-9A-Z]){2}([0-9A-Z]{3})?$');
    }
}
