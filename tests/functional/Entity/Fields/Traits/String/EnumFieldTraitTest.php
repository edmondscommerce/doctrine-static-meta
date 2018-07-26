<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class EnumFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/EnumFieldTraitTest/';
    protected const TEST_FIELD_FQN     = EnumFieldTrait::class;
    protected const TEST_FIELD_PROP    = EnumFieldInterface::PROP_ENUM;
    protected const TEST_FIELD_DEFAULT = EnumFieldInterface::DEFAULT_ENUM;
}
