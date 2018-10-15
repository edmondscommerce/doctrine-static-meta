<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EnumFakerData
 */
class EnumFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/EnumFieldTraitTest/';
    protected const TEST_FIELD_FQN     = EnumFieldTrait::class;
    protected const TEST_FIELD_PROP    = EnumFieldInterface::PROP_ENUM;
    protected const TEST_FIELD_DEFAULT = EnumFieldInterface::DEFAULT_ENUM;
    protected const VALID_VALUES       = EnumFieldInterface::ENUM_OPTIONS;
    protected const INVALID_VALUES     = [
        'cheese',
        '99',
    ];
}
