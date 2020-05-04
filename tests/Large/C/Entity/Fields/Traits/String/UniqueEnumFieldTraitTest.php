<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueEnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UniqueEnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EnumFakerData
 */
class UniqueEnumFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/EnumFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UniqueEnumFieldTrait::class;
    protected const TEST_FIELD_PROP    = UniqueEnumFieldInterface::PROP_UNIQUE_ENUM;
    protected const TEST_FIELD_DEFAULT = UniqueEnumFieldInterface::DEFAULT_UNIQUE_ENUM;
    protected const VALID_VALUES       = UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS;
    protected const INVALID_VALUES     = [
        'cheese',
        '99',
    ];
}
