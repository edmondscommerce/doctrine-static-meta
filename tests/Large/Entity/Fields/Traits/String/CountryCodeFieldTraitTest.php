<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class CountryCodeFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/CountryCodeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = CountryCodeFieldTrait::class;
    protected const TEST_FIELD_PROP    = CountryCodeFieldInterface::PROP_COUNTRY_CODE;
    protected const TEST_FIELD_DEFAULT = CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE;
}
