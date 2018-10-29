<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData
 */
class CountryCodeFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/CountryCodeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = CountryCodeFieldTrait::class;
    protected const TEST_FIELD_PROP    = CountryCodeFieldInterface::PROP_COUNTRY_CODE;
    protected const TEST_FIELD_DEFAULT = CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE;
    protected const VALID_VALUES       = [
        'AF',
        'DZ',
        'AI',
    ];
    protected const INVALID_VALUES     = [
        'USD',
        '705',
        'cheese',
    ];
}
