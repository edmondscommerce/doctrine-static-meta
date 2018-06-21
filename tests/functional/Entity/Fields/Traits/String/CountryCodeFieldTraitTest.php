<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;

class CountryCodeFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/CountryCodeFieldTraitTest/';
    protected const TEST_FIELD_FQN =   CountryCodeFieldTrait::class;
    protected const TEST_FIELD_PROP =  CountryCodeFieldInterface::PROP_COUNTRY_CODE;
    protected const TEST_FIELD_DEFAULT = CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE;
}
